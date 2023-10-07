<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Model\Message;

interface SyncMessagePactWriterInterface
{
    public function write(Message $message, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void;

    public function getPactPath(): string;
}
