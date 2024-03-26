<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\Message;

interface MessageGeneratorBuilderInterface
{
    public function build(Message $message, string $value): void;
}
