<?php

namespace PhpPact;

class PactFailureException extends \Exception
{
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (\is_callable('parent::__construct')) {
            parent::__construct($message, $code, $previous);
        }
    }
}
