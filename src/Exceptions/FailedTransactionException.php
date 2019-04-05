<?php


namespace Zarinpal\Exceptions;


use Exception;
use Throwable;

class FailedTransactionException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if ($message == "")
            $message = 'Transaction failed with error code: '.$code;
        parent::__construct($message, $code, $previous);
    }
}
