<?php

namespace PhpPact\Consumer\Driver\Pact;

use PhpPact\Consumer\Model\Pact\Pact;

interface PactDriverInterface
{
    public function cleanUp(): void;

    public function writePact(): void;

    public function getPact(): Pact;
}
