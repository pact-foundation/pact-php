<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface SyncMessagePactWriterInterface
{
    public function write(Message $message, PactPath $pactPath, WriteMode $mode = WriteMode::OVERWRITE): void;
}
