<?php

namespace App\Exception;

use Exception;
use Throwable;

class EmptyFileBodyException extends Exception
{
    /**
     * EmptyBodyException constructor.
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('The body of the file empty, please add data.', $code, $previous);
    }
}