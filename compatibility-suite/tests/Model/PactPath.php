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

    public function getProvider(): string
    {
        return self::PROVIDER;
    }

    public function __toString(): string
    {
        $pactDir = Path::PACTS_PATH;

        return "$pactDir/$this->consumer-{$this->getProvider()}.json";
    }
}
