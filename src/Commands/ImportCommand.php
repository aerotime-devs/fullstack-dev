<?php


namespace MekDrop\FlatLeadsDB\Commands;

use MekDrop\FlatLeadsDB\Services\PsiaudoDatabase;

/**
 * Imports leads command
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class ImportCommand extends \Ahc\Cli\Input\Command
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
        parent::__construct('import', 'Imports data from CSV file');

        $this->argument('<file>', 'File from where to import');

        $this->db = $database;
    }
}