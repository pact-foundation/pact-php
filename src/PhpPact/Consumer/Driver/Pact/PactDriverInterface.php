<?php

namespace PhpPact\Consumer\Driver\Pact;

interface PactDriverInterface
{
    public function getId(): int;

    public function cleanUp(): void;

    public function writePact(): void;
}
