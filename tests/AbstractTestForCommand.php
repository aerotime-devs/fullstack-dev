<?php

namespace MekDrop\FlatLeadsDB\Tests;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Faker\Factory;
use Faker\Generator;
use MekDrop\FlatLeadsDB\Models\Lead;
use MekDrop\FlatLeadsDB\Services\PseudoDatabase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestForCommand extends TestCase
{

    /**
     * @var PseudoDatabase
     */
    protected $db;
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var Lead[]
     */
    protected $leads = [];
    /**
     * @var Application|null
     */
    protected $app;
    /**
     * @var string
     */
    protected $outputStreamPath;
    /**
     * @var vfsStreamDirectory
     */
    protected $fs;

    public function setUp(): void
    {
        $this->fs = vfsStream::setup('test', 444);
        $this->db = new PseudoDatabase($this->fs->path());
        $this->faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $lead = new Lead();
            $lead->firstName = $this->faker->firstName;
            $lead->lastName = $this->faker->lastName;
            $lead->phone1 = $this->faker->phoneNumber;
            $lead->phone2 = $this->faker->phoneNumber;
            $lead->email = $this->faker->email;
            $lead->comment = $this->faker->realText(100);
            $this->leads[$lead->email] = $lead;
            $this->db->insert($lead);
        }

        $this->outputStreamPath = $this->fs->path() . 'console.txt.tmp';

        $this->app = new Application('test', '0.0.0', function ($exitCode = 0) {
        });
        $this->app->io(
            new Interactor(
                null,
                $this->outputStreamPath
            )
        );
    }

    public function tearDown(): void
    {
        $this->db->clear(Lead::class);
    }

    /**
     * Truncates output stream data
     */
    protected function truncateOutput(): void
    {
        $outputStream = fopen($this->outputStreamPath, 'w');
        ftruncate($outputStream, 0);
        fclose($outputStream);
    }

}