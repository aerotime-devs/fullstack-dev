<?php

namespace App\ConsoleCommand;

use App\Exception\FileNotFoundException;
use App\Helpers\PutDataInFile;
use App\Serializer\LeadJsonFileDecode;
use App\Serializer\LeadListEncode;
use App\Validation\Validation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterNewLead extends Command
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var LeadJsonFileDecode
     */
    private $leadJsonFileDecode;

    /**
     * @var LeadListEncode
     */
    private $leadListEncode;
    /**
     * @var PutDataInFile
     */
    private $putDataInFile;

    /**
     * RegisterNewLead constructor.
     * @param Validation $validation
     * @param LeadJsonFileDecode $leadJsonFileDecode
     * @param LeadListEncode $leadListEncode
     * @param PutDataInFile $putDataInFile
     */
    public function __construct(
        Validation $validation,
        LeadJsonFileDecode $leadJsonFileDecode,
        LeadListEncode $leadListEncode,
        PutDataInFile $putDataInFile
    ) {
        parent::__construct();

        $this->validation = $validation;
        $this->leadJsonFileDecode = $leadJsonFileDecode;
        $this->leadListEncode = $leadListEncode;
        $this->putDataInFile = $putDataInFile;
    }

    public function configure(): void
    {
        $this->setName('registerNewLead')
            ->setDescription('Register a new lead.')
            ->addArgument('firstname', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Last name')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('phonenumber1', InputArgument::REQUIRED, 'Phonenumber 1')
            ->addArgument('phonenumber2', InputArgument::REQUIRED, 'Phonenumber 2')
            ->addArgument('comment', InputArgument::REQUIRED, 'Comment');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws FileNotFoundException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fields = [
            'firstname'    => $input->getArgument('firstname'),
            'lastname'     => $input->getArgument('lastname'),
            'email'        => $input->getArgument('email'),
            'phonenumber1' => $input->getArgument('phonenumber1'),
            'phonenumber2' => $input->getArgument('phonenumber2'),
            'comment'      => $input->getArgument('comment'),
        ];

        if (!$this->validation->isEmailValid($input->getArgument('email'))) {
            $output->writeln($this->validation::PERSON_NOT_ADDED_IN_LEAD_LIST);
            $output->writeln($this->validation::EMAIL_NOT_VALID);

            return Command::FAILURE;
        }

        $decodedToArrayData = $this->leadJsonFileDecode->decodeFromJsonToArray();

        if (is_array($decodedToArrayData) &&
            $this->validation->isEmailInLeadList($input->getArgument('email'), $decodedToArrayData)
        ) {
            $output->writeln($this->validation::PERSON_EMAIL_ALREADY_IN_LIST);

            return Command::FAILURE;
        }

        $encodedToJsonData = $this->leadListEncode->leadListEncodeToJsonFormat($decodedToArrayData, $fields);

        if ($this->putDataInFile->putDataInJsonFile($encodedToJsonData)) {
            $output->writeln($this->validation::PERSON_ADDED_SUCCESS);

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}