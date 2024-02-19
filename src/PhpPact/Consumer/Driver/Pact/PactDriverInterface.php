<?php

namespace PhpPact\Consumer\Driver\Pact;

use PhpPact\Consumer\Model\Pact\Pact;

interface PactDriverInterface
{
    public function initWithLogLevel(): void;

    public function newPact(): Pact;

    public function deletePact(Pact $pact): void;

    public function writePact(Pact $pact): void;
}
