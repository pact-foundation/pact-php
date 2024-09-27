<?php

namespace PhpPact\Consumer\Driver\Exception;

class InteractionNotModifiedException extends DriverException
{
    public function __construct()
    {
        parent::__construct("The interaction can't be modified (i.e. the mock server for it has already started)");
    }
}
