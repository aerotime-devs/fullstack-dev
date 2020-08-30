<?php

use MekDrop\FlatLeadsDB\Commands\FindLeadCommand;
use MekDrop\FlatLeadsDB\Tests\AbstractTestForCommand;

class FindLeadCommandTest extends AbstractTestForCommand
{

    public function setUp(): void
    {
        parent::setUp();

        $this->app->add(
            new FindLeadCommand($this->db)
        );
    }

    public function testFindByFirstName()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '--first_name',
            $randomLead->firstName
        ]);

        $output1 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-f',
            $randomLead->firstName
        ]);
        $output2 = trim(file_get_contents($this->outputStreamPath));

        $this->assertSame($output1, $output2, 'Two outputs should return same result');
        $this->assertCount(5, explode(PHP_EOL, $output1), 'Less lines than expected');
        $this->assertStringContainsString($randomLead->firstName, $output2, 'No correct result seen');
    }

    public function testFindByLastName()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->app->handle([
            'console.php',
            'find',
            '--last_name',
            $randomLead->lastName
        ]);

        $output1 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-l',
            $randomLead->lastName
        ]);
        $output2 = trim(file_get_contents($this->outputStreamPath));

        $this->assertSame($output1, $output2, 'Two outputs should return same result');
        $this->assertCount(5, explode(PHP_EOL, $output1), 'Less lines than expected');
        $this->assertStringContainsString($randomLead->lastName, $output2, 'No correct result seen');
    }

    public function testFindByEmail()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->app->handle([
            'console.php',
            'find',
            '--email',
            $randomLead->email
        ]);

        $output1 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-e',
            $randomLead->email
        ]);
        $output2 = trim(file_get_contents($this->outputStreamPath));

        $this->assertSame($output1, $output2, 'Two outputs should return same result');
        $this->assertCount(5, explode(PHP_EOL, $output1), 'Less lines than expected');
        $this->assertStringContainsString($randomLead->email, $output2, 'No correct result seen');
    }

    public function testFindByPhone()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->app->handle([
            'console.php',
            'find',
            '--phone',
            $randomLead->phone1
        ]);

        $output1 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-p',
            $randomLead->phone1
        ]);
        $output2 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '--phone',
            $randomLead->phone2
        ]);

        $output3 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-p',
            $randomLead->phone2
        ]);
        $output4 = trim(file_get_contents($this->outputStreamPath));

        $this->assertSame($output1, $output2, 'Two outputs should return same result #1');
        $this->assertSame($output3, $output4, 'Two outputs should return same result #2');
        $this->assertSame($output1, $output3, 'Two outputs should return same result #3');
        $this->assertCount(5, explode(PHP_EOL, $output1), 'Less lines than expected');
        $this->assertStringContainsString($randomLead->phone1, $output2, 'No correct result seen');
    }

    public function testFindByComment()
    {
        $randomLead = $this->leads[array_rand($this->leads)];

        $this->app->handle([
            'console.php',
            'find',
            '--comment',
            $randomLead->comment
        ]);

        $output1 = trim(file_get_contents($this->outputStreamPath));

        $this->truncateOutput();
        $this->app->handle([
            'console.php',
            'find',
            '-c',
            $randomLead->comment
        ]);
        $output2 = trim(file_get_contents($this->outputStreamPath));

        $this->assertSame($output1, $output2, 'Two outputs should return same result');
        $this->assertCount(5, explode(PHP_EOL, $output1), 'Less lines than expected');
        $this->assertStringContainsString($randomLead->comment, $output2, 'No correct result seen');
    }

    public function testNotFind()
    {
        foreach (['--first_name', '-f', '--last_name', '-l', '--comment', '-c', '--phone', '-p', '--email', '-e'] as $param) {

            $this->truncateOutput();
            $this->app->handle([
                'console.php',
                'find',
                $param,
                $this->faker->realText(200) . $this->faker->email
            ]);

            $output = trim(file_get_contents($this->outputStreamPath));
            $this->assertStringContainsString('No entries found', $output, 'Output should contain "No entries found"');
        }
    }

}