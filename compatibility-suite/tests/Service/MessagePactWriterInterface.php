<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;

interface MessagePactWriterInterface
{
    public function write(string $name, string $body, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void;

    public function getPactPath(): string;
}
