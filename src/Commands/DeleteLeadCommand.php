<?php

namespace MekDrop\FlatLeadsDB\Commands;

use Ahc\Cli\Input\Command;
use MekDrop\FlatLeadsDB\Exceptions\NotFoundRecordException;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Services\PseudoDatabase;

/**
 * Command that deletes lead from local database
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class DeleteLeadCommand extends Command
{
    /**
     * @var PseudoDatabase
     */
    private $db;

    /**
     * @inheritDoc
     */
    public function __construct(PseudoDatabase $database)
    {
        parent::__construct('delete', 'Deletes existing lead');

        $this->argument('<email>', 'Email to search lead and delete');
        $this->db = $database;
    }

    /**
     * Execute command
     */
    public function execute()
    {
        $args = $this->args();

        try {
            $this->db->deleteById(Lead::class, $args['email']);
            $this->writer()->info('Lead deleted successfully!', true);
        } catch (NotFoundRecordException $exception) {
            $this->writer()->error('Record not found', true);
            return 2;
        }

        return 0;
    }

}