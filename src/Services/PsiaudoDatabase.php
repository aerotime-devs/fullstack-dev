<?php


namespace MekDrop\FlatLeadsDB\Services;

use League\Csv\AbstractCsv;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use MekDrop\FlatLeadsDB\Models\AbstractModel;
use ReflectionClass;
use ReflectionException;
use Sirius\Validation\ValueValidator;

/**
 * Service that lets work with CSV file as database
 *
 * @package MekDrop\FlatLeadsDB\Services
 */
class PsiaudoDatabase
{
    /**
     * Place where data is stored
     *
     * @var string
     */
    protected $path;

    /**
     * PsiaudoDatabase constructor.
     */
    public function __construct()
    {
        $this->path = dirname(dirname(__DIR__) ) . '/data/';
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
    protected function getFile(AbstractModel $model) {
        static $paths = [];
        $class = get_class($model);
        if (!isset($paths[$class])) {
            $paths[$class] = $this->path . (new ReflectionClass($model))->getShortName() . '.csv';
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
    protected function addValidationRules(Writer $csv, AbstractModel $model) {
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

        $id = array_search($model->getIdName(), array_keys($model->getValidationRules()), true);
        $csv->addValidator(function($row) use ($model, $id): bool {
            $reader = $this->createReader($model);
            $statement = new Statement();
            $records = $statement->where(
                static function ($crow) use ($id, $row) {
                    return $crow[$id] === array_values($row)[$id];
                }
            )->limit(1)->process($reader)->count();
            return $records === 0;
        }, $model->getIdName() . ' is not unique');

        return $csv;
    }

    /**
     * Creates reader
     *
     * @param AbstractModel $model Model to get reader for
     *
     * @return Reader
     *
     * @throws Exception
     * @throws ReflectionException
     */
    protected function createReader(AbstractModel $model) {
        $reader = Reader::createFromPath(
            $this->getFile($model),
            'r'
        );

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
     * Creates writer for model
     *
     * @param AbstractModel $model Model to write
     * @return Writer
     * @throws ReflectionException|Exception
     */
    protected function createWriter(AbstractModel $model) {
        $writer = Writer::createFromPath(
            $this->getFile($model),
            'a+'
        );

        $writer->setFlushThreshold(1);

        $this->setConfig($writer);

        return $this->addValidationRules($writer, $model);
    }

    /**
     * Insert
     *
     * @param AbstractModel|AbstractModel[] $model
     *
     * @throws CannotInsertRecord|Exception|ReflectionException
     */
    public function insert(AbstractModel $model) {
        $this
            ->createWriter($model)
            ->insertOne(
                $model->toArray()
            );
    }

}