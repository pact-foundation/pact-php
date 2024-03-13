<?php

namespace PhpPact\Consumer\Driver\Exception;

class PactFileNotWrittenException extends DriverException
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            1 => 'The function panicked.',
            2 => 'The pact file was not able to be written.',
            3 => 'The pact for the given handle was not found.',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
