<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface PactBrokerInterface
{
    public function publish(int $id): void;

    public function start(): void;

    public function stop(): void;

    public function getMatrix(): array;
}
