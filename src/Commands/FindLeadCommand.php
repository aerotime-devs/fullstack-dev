<?php


namespace MekDrop\FlatLeadsDB\Commands;

use MekDrop\FlatLeadsDB\Services\PsiaudoDatabase;

/**
 * Command that find lead in database
 *
 * @package MekDrop\FlatLeadsDB\Commands
 */
class FindLeadCommand extends \Ahc\Cli\Input\Command
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
        parent::__construct('find', 'Finds existing lead');

        $this
            ->option('-f --first_name', 'Search by first name')
            ->option('-l --last_name', 'Search by last name')
            ->option('-e --email', 'Search by email')
            ->option('-p --phone1', 'Search by phone1')
            ->option('--phone2', 'Search by phone2')
            ->option('-c --comment', 'Search by comment');

        $this->db = $database;
    }



}