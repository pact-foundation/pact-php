<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Model\VerifyResult;

interface MockServerInterface
{
    public function start(Pact $pact): void;

    public function verify(): VerifyResult;

    public function writePact(): void;
}
