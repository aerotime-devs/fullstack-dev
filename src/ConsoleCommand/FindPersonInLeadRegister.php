<?php

namespace App\ConsoleCommand;

use App\Exception\EmptyFileBodyException;
use App\Exception\FileNotFoundException;
use App\Search\Search;
use App\Serializer\LeadJsonFileDecode;
use App\Validation\Validation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindPersonInLeadRegister extends Command
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var LeadJsonFileDecode
     */
    private $leadJsonFileDecode;

    /**
     * FindPersonInLeadRegister constructor.
     * @param Search $search
     * @param Validation $validation
     * @param LeadJsonFileDecode $leadJsonFileDecode
     */
    public function __construct(Search $search, Validation $validation, LeadJsonFileDecode $leadJsonFileDecode)
    {
        parent::__construct();

        $this->validation = $validation;
        $this->search = $search;
        $this->leadJsonFileDecode = $leadJsonFileDecode;
    }

    public function configure(): void
    {
        $this->setName('findPersonInLeadRegister')
            ->setDescription('Find a person in the lead-register.')
            ->addArgument('firstname', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Last name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws FileNotFoundException|EmptyFileBodyException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $convertedArrayFromJsonData = $this->leadJsonFileDecode->decodeFromJsonToArray();

        if (!$convertedArrayFromJsonData) {
            throw new EmptyFileBodyException();
        }

        $searchResult = $this->search->search(
            $convertedArrayFromJsonData,
            [
                'firstname' => $input->getArgument('firstname'),
                'lastname'  => $input->getArgument('lastname')
            ]
        );

        if ($searchResult) {
            $table = new Table($output);

            $table->setHeaders(['firstname', 'lastname', 'email', 'phone1', 'phone2', 'comment'])
                ->setRows($searchResult)
                ->render();

            return Command::SUCCESS;
        }

        if (!$searchResult) {
            $output->writeln($this->validation::PERSON_NOT_FOUND);
        }

        return Command::FAILURE;
    }
}