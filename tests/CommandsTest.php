<?php

use MekDrop\FlatLeadsDB\Commands\DeleteLeadCommand;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class CommandsTest extends TestCase
{

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $fs;

    public function setUp(): void
    {
        // define my virtual file system
        $directory = [
            'json' => [
                'valid.json' => '{"VALID_KEY":123}',
                'invalid.json' => '{"test":123'
            ]
        ];
        // setup and cache the virtual file system
        $this->fs = vfsStream::setup('root', 444, [
            'Lead.csv' => "a;b;1@one.lt;+37067410411;;"
        ]);
    }
}
