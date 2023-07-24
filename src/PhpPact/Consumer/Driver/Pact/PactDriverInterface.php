<?php

namespace PhpPact\Consumer\Driver\Pact;

interface PactDriverInterface
{
    public function setUp(): void;

    public function cleanUp(): void;

    public function writePact(): void;
}
