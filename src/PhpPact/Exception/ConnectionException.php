<?php

namespace PhpPact\Exception;

use Exception;

/**
 * Unable to connect to server.
 */
class ConnectionException extends Exception
{
    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
