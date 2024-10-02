<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface PactWriterInterface
{
    public function write(int $id, PactPath $pactPath, WriteMode $mode = WriteMode::OVERWRITE): void;
}
