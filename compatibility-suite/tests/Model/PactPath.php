<?php

namespace PhpPactTest\CompatibilitySuite\Model;

use PhpPactTest\CompatibilitySuite\Constant\Path;

class PactPath
{
    public const PROVIDER = 'p';

    public function __construct(
        private string $consumer = 'c'
    ) {
    }

    public function getConsumer(): string
    {
        return $this->consumer;
    }

    public function __toString(): string
    {
        return sprintf("%s/%s-%s.json", Path::PACTS_PATH, $this->consumer, self::PROVIDER);
    }
}
