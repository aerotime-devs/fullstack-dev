<?php

namespace App\Serializer;

use App\Exception\FileNotFoundException;

class LeadJsonFileDecode
{
    public const DATA_FILE_PATH = 'src/Data/';
    public const DATA_FILE = 'data.json';

    /**
     * @param $fileName
     * @return mixed
     * @throws FileNotFoundException
     */
    public function decodeFromJsonToArray($fileName = self::DATA_FILE_PATH . self::DATA_FILE)
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException();
        }

        $currentData = file_get_contents($fileName);

        return json_decode($currentData, true);
    }
}