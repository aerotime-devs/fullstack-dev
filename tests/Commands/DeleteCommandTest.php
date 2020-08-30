<?php

use MekDrop\FlatLeadsDB\Commands\DeleteLeadCommand;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Tests\AbstractTestForCommand;

class DeleteCommandTest extends AbstractTestForCommand
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->add(
            new DeleteLeadCommand($this->db)
        );
    }

    public function testDeleteLeadWithGoodEmailCommand()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->app->handle([
            'console.php',
            'delete',
            $randomLead->email
        ]);

        $this->assertStringContainsString('Lead deleted successfully!', file_get_contents($this->outputStreamPath), 'Problem with app output message');
        $this->assertCount(0, iterator_to_array($this->db->findBy(Lead::class, ['email' => $randomLead->email])), 'Problem with deletion');
        $this->assertNotCount(count($this->leads), iterator_to_array($this->db->findBy(Lead::class, [])), 'Problem with deletion #2');
    }

    public function testDeleteLeadWithBadEmailCommand()
    {
        while (isset($this->leads[$badEmail = $this->faker->email])) ;

        $this->app->handle([
            'console.php',
            'delete',
            $badEmail
        ]);

        $this->assertStringContainsString('Record not found', file_get_contents($this->outputStreamPath), 'Problem with app output message');
        $this->assertCount(count($this->leads), iterator_to_array($this->db->findBy(Lead::class, [])), 'Problem with deletion');
    }

}
