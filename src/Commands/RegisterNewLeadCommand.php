<?php

namespace MekDrop\FlatLeadsDB\Commands;

use Ahc\Cli\IO\Interactor;
use League\Csv\CannotInsertRecord;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Services\PsiaudoDatabase;
use Sirius\Validation\Validator;

/**
 * Command that registers new lead
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class RegisterNewLeadCommand extends \Ahc\Cli\Input\Command
{
    /**
     * @var PsiaudoDatabase
     */
    private $db;

    /**
     * @inheritDoc
     */
    public function __construct(PsiaudoDatabase $database)
    {
        parent::__construct('register', 'Registers new lead');

        $this->argument('<first_name>', 'First name of lead')
            ->argument('<last_name>', 'Last name of lead')
            ->argument('<email>', 'Email of lead')
            ->argument('<phone1>', 'Phone #1')
            ->argument('[phone2]', 'Phone #2')
            ->argument('[comment]', 'Any comment');

        $this->db = $database;
    }

    /**
     * Execute command
     */
    public function execute() {
        $args = $this->args();

        $lead = new Lead();
        $lead->comment = $args['comment'];
        $lead->phone1 = $args['phone1'];
        $lead->phone2 = $args['phone2'];
        $lead->firstName = $args['firstName'];
        $lead->lastName = $args['lastName'];
        $lead->email = $args['email'];

        try {
            $this->db->insert($lead);
            $this->writer()->info('Lead added successfully!');
        } catch (CannotInsertRecord $exception) {
            $this->writer()->error($exception->getMessage() . ' - ' . $exception->getName(), true);
        }
    }

}