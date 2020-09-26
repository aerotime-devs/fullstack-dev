<?php

namespace App\Helpers;

use App\Exception\FileNotFoundException;
use App\Serializer\LeadJsonFileDecode;

class PutDataInFile
{
    /**
     * @var LeadJsonFileDecode
     */
    private $leadCsvFileDecode;

    /**
     * PutDataInFile constructor.
     * @param LeadJsonFileDecode $leadJsonFileDecode
     */
    public function __construct(LeadJsonFileDecode $leadJsonFileDecode)
    {
        $this->leadCsvFileDecode = $leadJsonFileDecode;
    }

    /**
     * @param $leadListJsonData
     * @return false|int
     * @throws FileNotFoundException
     */
    public function putDataInJsonFile($leadListJsonData)
    {
        $jsonFileLocation = $this->leadCsvFileDecode::DATA_FILE_PATH . $this->leadCsvFileDecode::DATA_FILE;

        if (!file_exists($jsonFileLocation)) {
            throw new FileNotFoundException();
        }

        return file_put_contents($jsonFileLocation, $leadListJsonData);
    }
}