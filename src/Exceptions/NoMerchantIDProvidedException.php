<?php


namespace Zarinpal\Exceptions;


use Exception;
use Throwable;

class NoMerchantIDProvidedException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $message = 'No MerchantId provided in services configuration file.';
        $code = -1;
        parent::__construct($message, $code, $previous);
    }
}
