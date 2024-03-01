<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Standalone\MockService\Model\VerifyResult;

interface MockServerInterface
{
    public function start(): void;

    public function verify(): VerifyResult;

    public function writePact(): void;

    public function cleanUp(): void;
}
