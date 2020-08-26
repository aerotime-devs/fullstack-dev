<?php

namespace MekDrop\FlatLeadsDB\Commands;

use MekDrop\FlatLeadsDB\Services\PsiaudoDatabase;

/**
 * Command that deletes lead from local database
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class DeleteLeadCommand extends \Ahc\Cli\Input\Command
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
        parent::__construct('delete', 'Deletes existing lead');

        $this->argument('<email>', 'Email to search lead and delete');
        $this->db = $database;
    }

}