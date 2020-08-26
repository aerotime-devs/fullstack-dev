<?php

namespace MekDrop\FlatLeadsDB\Exceptions;

use League\Csv\Exception;
use Throwable;

/**
 * Exception when record not found
 *
 * @package MekDrop\FlatLeadsDB\Exceptions
 */
class NotFoundRecordException extends Exception
{

    public function __construct($id = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($id . ' not found in database', $code, $previous);
    }

}