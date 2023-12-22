<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;

interface PactWriterInterface
{
    public function write(int $id, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void;

    public function getPactPath(): string;
}
