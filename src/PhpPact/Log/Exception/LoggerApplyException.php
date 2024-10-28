<?php

namespace PhpPact\Log\Exception;

class LoggerApplyException extends LoggerException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            -1 => "Can't set logger (applying the logger failed, perhaps because one is applied already).",
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
