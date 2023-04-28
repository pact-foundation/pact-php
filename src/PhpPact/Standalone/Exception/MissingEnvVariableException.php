<?php

namespace PhpPact\Standalone\Exception;

use Exception;

/**
 * Exception for a required environmental variable.
 */
class MissingEnvVariableException extends Exception
{
    public function __construct(string $variableName)
    {
        parent::__construct("Please provide required environmental variable {$variableName}!", 0, null);
    }
}
