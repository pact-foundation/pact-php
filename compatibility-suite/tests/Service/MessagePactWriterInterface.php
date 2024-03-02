<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

interface MessagePactWriterInterface
{
    public function write(Message $message, PactPath $pactPath, string $mode = PactConfigInterface::MODE_OVERWRITE): void;
}
