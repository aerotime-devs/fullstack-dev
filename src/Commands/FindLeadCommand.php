<?php

namespace MekDrop\FlatLeadsDB\Commands;

use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Services\PseudoDatabase;

/**
 * Command that find lead in database
 *
 * @package MekDrop\FlatLeadsDB\Commands
 *
 * @property string $firstName First name for search
 * @property string $lastName Last name for search
 * @property string $email Email for search
 * @property string $phone Phone for search
 * @property string $comment Comment for search
 */
class FindLeadCommand extends \Ahc\Cli\Input\Command
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
        parent::__construct('find', 'Finds existing lead by partial data');

        $this
            ->option('-f --first_name', 'Search by first name')
            ->option('-l --last_name', 'Search by last name')
            ->option('-e --email', 'Search by email')
            ->option('-p --phone', 'Search by phone')
            ->option('-c --comment', 'Search by comment');

        $this->db = $database;
    }

    /**
     * Execute command
     */
    public function execute() {
        $searchRules = [];
        foreach (['firstName', 'lastName', 'email', 'comment'] as $field) {
            $searchRules[$field] = $this->$field;
        }
        if ($this->phone) {
            $searchRules['phone1'] = $this->phone;
            $searchRules['phone2'] = $this->phone;
        }

        $rows = [];
        foreach($this->db->findBy(Lead::class, $searchRules) as $lead) {
            $rows[] = $lead->toArray();
        }

        if (!empty($rows)) {
            $this->writer()->table($rows);
        } else {
            $this->writer()->info('No entries found');
        }
    }

}