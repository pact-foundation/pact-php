<?php

namespace PhpPact\Consumer\Exception;

use Exception;

/**
 * Unable to verify that the mock server is running successfully.
 * Class HealthCheckFailedException
 */
class HealthCheckFailedException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
