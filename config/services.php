<?php

/**
 * This file defines all services in container
 */

use League\Container\Definition\Definition;
use League\Container\Definition\DefinitionAggregate;
use MekDrop\FlatLeadsDB\Commands\{DeleteLeadCommand,FindLeadCommand,ImportCommand,RegisterNewLeadCommand};
use MekDrop\FlatLeadsDB\Services\PsiaudoDatabase;

return new DefinitionAggregate([
    new Definition('db', PsiaudoDatabase::class),
    (new Definition(RegisterNewLeadCommand::class))->addArgument('db')->addTag('command'),
    (new Definition(DeleteLeadCommand::class))->addArgument('db')->addTag('command'),
    (new Definition(FindLeadCommand::class))->addArgument('db')->addTag('command'),
    (new Definition(ImportCommand::class))->addArgument('db')->addTag('command'),
]);