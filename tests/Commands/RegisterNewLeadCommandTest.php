<?php

use MekDrop\FlatLeadsDB\Commands\RegisterNewLeadCommand;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Tests\AbstractTestForCommand;

class RegisterNewLeadCommandTest extends AbstractTestForCommand
{

    public function setUp(): void
    {
        parent::setUp();

        $this->app->add(
            new RegisterNewLeadCommand($this->db)
        );
    }

    public function testRegisterWithAllGoodData()
    {
        $this->app->handle([
            'console.php',
            'register',
            $this->faker->firstName,
            $this->faker->lastName,
            $email = $this->faker->email,
            $this->faker->phoneNumber,
            $this->faker->phoneNumber,
            $this->faker->realText()
        ]);

        $this->assertStringContainsString('Lead added successfully!', file_get_contents($this->outputStreamPath), 'Not message for insert success');
        $this->assertCount(count($this->leads) + 1, iterator_to_array($this->db->findBy(Lead::class, [])), 'Count not increased');
        $this->assertCount(1, iterator_to_array($this->db->findBy(Lead::class, ['email' => $email])), 'Not found new record');
    }

    public function testRegisterWithoutNotRequiredData()
    {
        $this->app->handle([
            'console.php',
            'register',
            $this->faker->firstName,
            $this->faker->lastName,
            $email = $this->faker->email,
            $this->faker->phoneNumber
        ]);

        $this->assertStringContainsString('Lead added successfully!', file_get_contents($this->outputStreamPath), 'Not message for insert success');
        $this->assertCount(count($this->leads) + 1, iterator_to_array($this->db->findBy(Lead::class, [])), 'Count not increased');
        $this->assertCount(1, iterator_to_array($this->db->findBy(Lead::class, ['email' => $email])), 'Not found new record');
    }

}