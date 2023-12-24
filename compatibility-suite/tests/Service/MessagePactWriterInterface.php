<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface MessagePactWriterInterface
{
    public function write(string $name, string $body, PactPath $pactPath, string $mode = PactConfigInterface::MODE_OVERWRITE): void;
}
