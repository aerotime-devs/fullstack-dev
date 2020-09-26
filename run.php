#/usr/bin/env php

<?php

use App\ConsoleCommand\DeletePersonFromLeadRegisterByEmail;
use App\ConsoleCommand\FindPersonInLeadRegister;
use App\ConsoleCommand\ImportPersonsUsingCsvFile;
use App\ConsoleCommand\RegisterNewLead;
use App\Helpers\PutDataInFile;
use App\Helpers\SplFile;
use App\Search\Search;
use App\Serializer\LeadJsonFileDecode;
use App\Serializer\LeadListEncode;
use App\Validation\Validation;
use Symfony\Component\Console\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application('Aero time', '1.0');

$app->add(
    new DeletePersonFromLeadRegisterByEmail(
        new Search(),
        new LeadJsonFileDecode(),
        new PutDataInFile(new LeadJsonFileDecode()),
        new LeadListEncode(),
        new Validation()
    )
);
$app->add(new FindPersonInLeadRegister(new Search(), new Validation(), new LeadJsonFileDecode()));
$app->add(
    new ImportPersonsUsingCsvFile(
        new LeadJsonFileDecode,
        new PutDataInFile(new LeadJsonFileDecode()),
        new Validation(),
        new SplFile()
    )
);
$app->add(
    new RegisterNewLead(
        new Validation(),
        new LeadJsonFileDecode(),
        new LeadListEncode(),
        new PutDataInFile(new LeadJsonFileDecode())
    )
);

$app->run();
