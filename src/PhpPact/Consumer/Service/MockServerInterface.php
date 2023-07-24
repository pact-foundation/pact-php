<?php

namespace PhpPact\Consumer\Service;

interface MockServerInterface
{
    public function start(): void;

    public function verify(): bool;
}
