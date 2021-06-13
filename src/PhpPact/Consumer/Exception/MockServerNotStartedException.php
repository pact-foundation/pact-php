<?php

namespace PhpPact\Consumer\Exception;

use Exception;

/**
 * Mock server is not started.
 * Class MockServerNotStartedException.
 */
class MockServerNotStartedException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
