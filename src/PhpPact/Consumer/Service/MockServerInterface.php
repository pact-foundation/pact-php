<?php

namespace PhpPact\Consumer\Service;

interface MockServerInterface
{
    public function start(): void;

    public function isMatched(): bool;

    public function writePact(): void;

    public function cleanUp(): void;
}
