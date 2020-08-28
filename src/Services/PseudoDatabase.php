<?php

namespace MekDrop\FlatLeadsDB\Services;

use Generator;
use League\Csv\AbstractCsv;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use MekDrop\FlatLeadsDB\Exceptions\NotFoundRecordException;
use MekDrop\FlatLeadsDB\Models\AbstractModel;
use ReflectionClass;
use ReflectionException;
use Sirius\Validation\ValueValidator;

/**
 * Service that lets work with CSV file as database
 *
 * @package MekDrop\FlatLeadsDB\Services
 */
class PseudoDatabase
{
    /**
     * Place where data is stored
     *
     * @var string
     */
    protected $path;

    /**
     * PsiaudoDatabase constructor.
     *
     * @param string|null $path If path specified, use this path as file storage place
     */
    public function __construct(?string $path = null)
    {
        if ($path === null) {
            $this->path = dirname(dirname(__DIR__)) . '/data/';
        } else {
            $this->path = $path;
        }
    }

    /**
     * Gets filename for model
     *
     * @param AbstractModel $model
     *
     * @return string
     *
     * @throws ReflectionException
     */
    protected function getFileFromModel(AbstractModel $model): string
    {
        return $this->getFileFromClass(
          get_class($model)
        );
    }

    /**
     * Gets filename for class
     *
     * @param string $class Class for what get filename
     *
     * @return string
     *
     * @throws ReflectionException
     */
    protected function getFileFromClass(string $class): string
    {
        static $paths = [];
        if (!isset($paths[$class])) {
            $paths[$class] = $this->path . (new ReflectionClass($class))->getShortName() . '.csv';
        }
        return $paths[$class];
    }

    /**
     * Adds validation rules to writer
     *
     * @param Writer $csv CSV writer
     * @param AbstractModel $model Model for writer
     *
     * @return Writer
     */
    protected function addValidationRules(Writer $csv, AbstractModel $model): Writer
    {
        foreach ($model->getValidationRules() as $column => $validationRule) {
            if ($validationRule === null) {
                continue;
            }
            $csv->addValidator(
                static function (array $row) use ($column, $validationRule): bool {
                    $valueValidator = new ValueValidator();
                    $valueValidator->add($validationRule);
                    return $valueValidator->validate($row[$column]);
                }, $column . ' is not valid'
            );
        }

        $csv->addValidator(function($row) use ($model): bool {
            $reader = $this->createReader(
                get_class($model)
            );
            $statement = new Statement();
            $idIndex = $this->getIdFieldIndex($model);
            $idValue = array_values($row)[$idIndex];
            $records = $statement->where(
                static function ($crow) use ($idIndex, $idValue) {
                    return $crow[$idIndex] === $idValue;
                }
            )->limit(1)->process($reader)->count();
            return $records === 0;
        }, $model->getIdName() . ' is not unique');

        return $csv;
    }

    /**
     * Creates reader
     *
     * @param string $modelClass Model class name
     * @param bool $isTempCopy If true reads from copied temp file
     * @return Reader
     *
     * @throws Exception
     * @throws ReflectionException
     */
    protected function createReader(string $modelClass, $isTempCopy = false): Reader
    {
        $filename = $this->getFileFromClass($modelClass);

        if ($isTempCopy) {
            copy($filename, $filename . '.tmp');
            $filename .= '.tmp';
        }

        $reader = Reader::createFromPath($filename, 'r');

        $this->setConfig($reader);

        return $reader;
    }

    /**
     * Sets config for reader or writer
     *
     * @param AbstractCsv $csv Reader or writer
     * @throws Exception
     */
    protected function setConfig(AbstractCsv $csv) {
        $csv->setDelimiter(';');
    }

    /**
     * Insert
     *
     * @param AbstractModel|AbstractModel[] $model
     *
     * @throws CannotInsertRecord|Exception|ReflectionException
     */
    public function insert(AbstractModel $model) {
        $writer = Writer::createFromPath(
            $this->getFileFromModel($model),
            'a+'
        );

        $writer->setFlushThreshold(1);

        $this->setConfig($writer);

        $this
            ->addValidationRules($writer, $model)
            ->insertOne(
                $model->toArray()
            );
    }

    /**
     * Delete by id
     *
     * @param string $modelClass Model class where to delete item
     * @param string $id ID of record to delete
     *
     * @throws Exception
     * @throws ReflectionException
     */
    public function deleteById(string $modelClass, string $id) {
        $reader = $this->createReader($modelClass, true);
        $filename = mb_substr($reader->getPathname(), 0, -4);

        $statement = new Statement();
        $idIndex = $this->getIdFieldIndex(
            new $modelClass()
        );
        $processed = $statement->where(
            static function ($crow) use ($id, $idIndex) {
                return $crow[$idIndex] === $id;
            }
        )->process($reader);

        if ($reader->count() === $processed->count()) {
            throw new NotFoundRecordException($id);
        }

        $writer = Writer::createFromPath(
            $filename,
            'w'
        );
        $this->setConfig($writer);

        $writer->insertAll(
            $processed->getRecords()
        );
        $writer->setFlushThreshold(1);

        @unlink($reader->getPathname());
    }

    /**
     * Get id field index for model
     *
     * @param AbstractModel $model Model for what get ID field index
     *
     * @return int
     */
    protected function getIdFieldIndex(AbstractModel $model): int
    {
        return array_search(
            $model->getIdName(),
            array_keys(
                $model->getValidationRules()
            ),
            true
        );
    }

    /**
     * Find records by partial data (using OR operator if multiple where rules)
     *
     * @param string $modelClass Model class where to search
     * @param array $searchRules Search rules (if empty returns all)
     *
     * @return Generator
     *
     * @throws Exception
     * @throws ReflectionException
     */
    public function findBy(string $modelClass, array $searchRules): Generator
    {
        $reader = $this->createReader($modelClass);

        $model = new $modelClass();
        $indexMap = array_flip(
            array_keys($model->getValidationRules())
        );

        $searchRules = array_filter($searchRules);

        if (!empty($searchRules)) {
            $statement = new Statement();
            $processed = $statement->where(function ($row) use ($searchRules, $indexMap) {
                foreach ($searchRules as $column => $search) {
                    $index = $indexMap[$column];
                    if (is_string($row[$index]) && mb_strpos($row[$index], $search) !== false) {
                        return true;
                    }
                }
                return false;
            })->process($reader)->getRecords();
        } else {
            $processed = $reader->getRecords();
        }
        foreach ($processed as $row) {
            yield new $modelClass(
                array_combine(
                    array_flip($indexMap),
                    $row
                )
            );
        }
    }
}