<?php

namespace MekDrop\FlatLeadsDB\Commands;

use League\Csv\CannotInsertRecord;
use League\Csv\Reader;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Services\PseudoDatabase;

/**
 * Imports leads command
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class ImportCommand extends \Ahc\Cli\Input\Command
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
        parent::__construct('import', 'Imports data from CSV file');

        $this->argument('<file>', 'File from where to import');

        $this->db = $database;
    }

    /**
     * Executes command
     */
    public function execute() {
        $args = $this->args();
        $file = $args['file'];

        if (!file_exists($file)) {
            $this->writer()->error('File not exists', true);
            return;
        }

        $reader = Reader::createFromPath($file, 'r');
        $reader->setDelimiter(';');
        $lineNo = 0;
        $this->writer()->comment('Importing...', true);
        $good = 0;
        $bad = 0;
        foreach ($reader->getRecords() as $record) {
            $this->writer()->info(' #' . (++$lineNo));
            try {
                if (count($record) < 6) {
                    $this->writer()->warn(' wasn\'t added - wrong data', true);
                    $bad++;
                    continue;
                }
                $lead = new Lead();
                list($lead->firstName, $lead->lastName, $lead->email, $lead->phone1, $lead->phone2, $lead->comment) = $record;
                $this->db->insert($lead);
                $this->writer()->info(' added', true);
                $good++;
            } catch (CannotInsertRecord $exception) {
                $this->writer()->warn(' wasn\'t added - ' . $exception->getName(), true);
                $bad++;
            }
        }
        $this->writer()->comment(
            sprintf('%d results were added and %d were skipped', $good, $bad)
        );
    }
}