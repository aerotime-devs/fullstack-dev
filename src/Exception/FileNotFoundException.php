<?php

namespace App\Exception;

use Exception;
use Throwable;

class FileNotFoundException extends Exception
{
    /**
     * EmptyBodyException constructor.
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('File not found!', $code, $previous);
    }
}