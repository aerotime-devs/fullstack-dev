<?php

namespace App\Helpers;

use App\Exception\EmptyFileBodyException;
use App\Exception\FileFormatNotSupportedException;
use App\Exception\FileNotFoundException;
use SplFileObject;

class SplFile
{
    /**
     * @param $fileName
     * @return false|SplFileObject
     * @throws FileNotFoundException|FileFormatNotSupportedException
     */
    public function splFileObject($fileName)
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException();
        }

        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'csv') {
            throw new FileFormatNotSupportedException();
        }

        return new SplFileObject($fileName);
    }

    /**
     * @param $fileName
     * @return false|string[]
     * @throws EmptyFileBodyException
     * @throws FileNotFoundException|FileFormatNotSupportedException
     */
    public function csvFileHeader($fileName)
    {
        $splFileObject = $this->splFileObject($fileName);

        if (empty($splFileObject->current()[0])) {
            throw new EmptyFileBodyException();
        }

        return explode(";", $splFileObject->current(), -1);
    }
}