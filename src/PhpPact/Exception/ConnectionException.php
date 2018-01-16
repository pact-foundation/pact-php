<?php

namespace PhpPact\Exception;

use Exception;

/**
 * Unable to connect to server.
 * Class ConnectionException
 */
class ConnectionException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0, null);
    }
}
