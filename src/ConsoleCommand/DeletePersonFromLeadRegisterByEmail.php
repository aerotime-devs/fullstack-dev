<?php

namespace App\ConsoleCommand;

use App\Exception\FileNotFoundException;
use App\Helpers\PutDataInFile;
use App\Search\Search;
use App\Serializer\LeadJsonFileDecode;
use App\Serializer\LeadListEncode;
use App\Validation\Validation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeletePersonFromLeadRegisterByEmail extends Command
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @var LeadJsonFileDecode
     */
    private $leadJsonFileDecode;

    /**
     * @var PutDataInFile
     */
    private $putDataInFile;

    /**
     * @var LeadListEncode
     */
    private $leadListEncode;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * DeletePersonFromLeadRegisterByEmail constructor.
     * @param Search $search
     * @param LeadJsonFileDecode $leadJsonFileDecode
     * @param PutDataInFile $putDataInFile
     * @param LeadListEncode $leadListEncode
     * @param Validation $validation
     */
    public function __construct(
        Search $search,
        LeadJsonFileDecode $leadJsonFileDecode,
        PutDataInFile $putDataInFile,
        LeadListEncode $leadListEncode,
        Validation $validation
    ) {
        parent::__construct();

        $this->leadJsonFileDecode = $leadJsonFileDecode;
        $this->search = $search;
        $this->putDataInFile = $putDataInFile;
        $this->leadListEncode = $leadListEncode;
        $this->validation = $validation;
    }

    public function configure(): void
    {
        $this->setName('deletePersonFromLeadRegisterByEmail')
            ->setDescription('Delete a person from the lead-register by Email.')
            ->addArgument('email', InputArgument::REQUIRED, 'Email');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws FileNotFoundException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $decodedToArrayData = $this->leadJsonFileDecode->decodeFromJsonToArray();

        if (!$decodedToArrayData) {
            $output->writeln($this->validation::PERSON_NOT_FOUND);

            return Command::FAILURE;
        }

        $arrayKey = $this->search->searchPersonByEmailFromArray($decodedToArrayData,
            $input->getArgument('email'));

        if ($arrayKey !== false) {
            unset($decodedToArrayData[$arrayKey]);

            $encodedToJsonData = $this->leadListEncode->removeArrayKeys($decodedToArrayData);

            if ($this->putDataInFile->putDataInJsonFile($encodedToJsonData)) {
                $output->writeln($this->validation::PERSON_DELETED_FROM_FILE);

                return Command::SUCCESS;
            }
        }

        $output->writeln($this->validation::PERSON_NOT_FOUND);

        return Command::FAILURE;
    }
}