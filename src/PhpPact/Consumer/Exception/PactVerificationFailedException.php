<?php

namespace PhpPact\Consumer\Exception;

use Exception;

/**
 * Failed to verify that all PACT interactions were tested.
 * Class PactVerificationFailedException
 */
class PactVerificationFailedException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
