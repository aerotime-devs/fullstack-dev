<?php

namespace App\ConsoleCommand;

use App\Exception\EmptyFileBodyException;
use App\Exception\FileFormatNotSupportedException;
use App\Exception\FileNotFoundException;
use App\Helpers\PutDataInFile;
use App\Helpers\SplFile;
use App\Serializer\LeadJsonFileDecode;
use App\Validation\Validation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPersonsUsingCsvFile extends Command
{
    /**
     * @var LeadJsonFileDecode
     */
    private $leadJsonFileDecode;

    /**
     * @var PutDataInFile
     */
    private $putDataInFile;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var SplFile
     */
    private $splFile;

    /**
     * ImportPersonsUsingCsvFile constructor.
     * @param LeadJsonFileDecode $leadJsonFileDecode
     * @param PutDataInFile $putDataInFile
     * @param Validation $validation
     * @param SplFile $splFile
     */
    public function __construct(
        LeadJsonFileDecode $leadJsonFileDecode,
        PutDataInFile $putDataInFile,
        Validation $validation,
        SplFile $splFile
    ) {
        parent::__construct();

        $this->leadJsonFileDecode = $leadJsonFileDecode;
        $this->putDataInFile = $putDataInFile;
        $this->validation = $validation;
        $this->splFile = $splFile;
    }

    public function configure(): void
    {
        $this->setName('importPersonsUsingCsvFile')
            ->setDescription('Import of persons using a CSV-file.')
            ->addArgument('fileName', InputArgument::REQUIRED, 'Csv file name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws FileNotFoundException|EmptyFileBodyException|FileFormatNotSupportedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $convertedArray = [];
        $invalidEmailsArray = [];
        $currentData = $this->leadJsonFileDecode->decodeFromJsonToArray();

        if (!is_array($currentData)) {
            $currentData = [];
        }

        $csvFile = $this->splFile->splFileObject($input->getArgument('fileName'));
        $csvFileHeader = $this->splFile->csvFileHeader($input->getArgument('fileName'));
        $csvFileHeaderCount = count($csvFileHeader);

        if (!$csvFileHeaderCount) {
            $output->writeln($this->validation::CSV_FILE_HEADER_IS_EMPTY);

            return Command::FAILURE;
        }

        for ($x = 0; !$csvFile->eof(); ++$x) {
            $combinedArrayKeyAndValue = false;
            $checkingIfEmailValid = false;

            $csvFileRowDataConvertedToArray = explode(";", $csvFile->fgets(), -1);
            $numberOfConvertedArrayRow = count($csvFileRowDataConvertedToArray);

            if(!$numberOfConvertedArrayRow) {
                continue;
            }

            if ($numberOfConvertedArrayRow !== $csvFileHeaderCount) {
                $output->writeln($this->validation::FILE_HEADER_AND_ROW_DATA_ERROR);

                return Command::FAILURE;
            }

            if ($csvFileRowDataConvertedToArray) {
                $combinedArrayKeyAndValue = array_combine($csvFileHeader, $csvFileRowDataConvertedToArray);
                $checkingIfEmailValid = $this->validation->isEmailValid($combinedArrayKeyAndValue['email']);
            }

            if (
                $checkingIfEmailValid &&
                !$this->validation->isEmailInLeadList($combinedArrayKeyAndValue['email'], $currentData)
            ) {
                $convertedArray[] = $combinedArrayKeyAndValue;
            }

            if (
                $x && (
                    !$checkingIfEmailValid ||
                    $this->validation->isEmailInLeadList($combinedArrayKeyAndValue['email'], $currentData)
                )
            ) {
                $invalidEmailsArray[] = $combinedArrayKeyAndValue;
            }
        }

        $decodedToArrayData = $this->leadJsonFileDecode->decodeFromJsonToArray();

        $newImportRows = count($convertedArray);

        if (!$newImportRows) {
            $output->writeln($this->validation::IMPORTED_IN_FILE . $newImportRows);

            $this->generateDuplicateRowsTable($output, $invalidEmailsArray);

            return Command::FAILURE;
        }

        $convertedArrayWithoutDuplicates = $this->validation->removeDuplicatedRowsInArray($convertedArray);

        $encodedToJsonData = json_encode(
            $this->mergeConvertedDataWithCurrent($decodedToArrayData, $convertedArrayWithoutDuplicates)
        );

        if ($this->putDataInFile->putDataInJsonFile($encodedToJsonData)) {
            $output->writeln($this->validation::CSV_FILE_IMPORTED_SUCCESS);

            $this->generateDuplicateRowsTable($output, $invalidEmailsArray);

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    /**
     * @param $currentData
     * @param $convertedArray
     * @return array
     */
    private function mergeConvertedDataWithCurrent($currentData, $convertedArray): array
    {
        if ($currentData) {
            return array_merge($currentData, $convertedArray);
        }

        return $convertedArray;
    }

    private function generateDuplicateRowsTable($output, $dataArray): void
    {
        if (!empty($dataArray)) {
            $output->writeln($this->validation::DUPLICATE_ROWS . count($dataArray));

            $table = new Table($output);

            $table->setHeaders(['firstname', 'lastname', 'email', 'phone1', 'phone2', 'comment'])
                ->setRows($dataArray)
                ->render();
        }
    }
}