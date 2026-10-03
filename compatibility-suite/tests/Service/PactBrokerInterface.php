<?php

namespace PhpPactTest\CompatibilitySuite\Service;

interface PactBrokerInterface
{
    public function publish(int $id): void;

    public function start(): void;

    public function stop(): void;

    /**
     * @return array<array-key, mixed>
     */
    public function getMatrix(): array;
}
