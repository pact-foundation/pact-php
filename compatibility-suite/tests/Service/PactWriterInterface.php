<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface PactWriterInterface
{
    public function write(int $id, PactPath $pactPath, string $mode = PactConfigInterface::MODE_OVERWRITE): void;
}
