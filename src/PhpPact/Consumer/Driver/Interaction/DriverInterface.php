<?php

namespace PhpPact\Consumer\Driver\Interaction;

interface DriverInterface
{
    public function writePactAndCleanUp(): void;
}
