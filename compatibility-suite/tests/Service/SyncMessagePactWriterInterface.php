<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\SyncMessage\Model\SyncMessage;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface SyncMessagePactWriterInterface
{
    public function write(SyncMessage $message, PactPath $pactPath, WriteMode $mode = WriteMode::OVERWRITE): void;
}
