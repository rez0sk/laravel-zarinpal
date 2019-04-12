<?php

namespace Zarinpal\Exceptions;

use Exception;
use Throwable;

class InvalidDataException extends Exception
{
    protected $context;

    public function __construct($message = '', $code = 0, $context = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(array $context)
    {
        return $this->context;
    }
}
