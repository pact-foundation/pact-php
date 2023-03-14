<?php

namespace PhpPact\Consumer\Exception;

use Exception;

/**
 * Class MockServerNotStartedException.
 */
class MockServerNotStartedException extends Exception
{
    public function __construct(int $code)
    {
        $message = match ($code) {
            -1 => 'An invalid handle was received. Handles should be created with `pactffi_new_pact`',
            -2 => 'Transport_config is not valid JSON',
            -3 => 'The mock server could not be started',
            -4 => 'The method panicked',
            -5 => 'The address is not valid',
            default => 'Unknown error',
        };
        parent::__construct($message, $code);
    }
}
