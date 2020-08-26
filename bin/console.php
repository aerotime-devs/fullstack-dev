#!/usr/bin/env php
<?php

use Ahc\Cli\Application;
use League\Container\Container;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container(
    require dirname(__DIR__) . '/config/services.php'
);

$app = new Application('Flat Leads Database', '0.0.1');
foreach($container->get('command') as $command) {
    $app->add($command);
}
$app->handle($_SERVER['argv']);