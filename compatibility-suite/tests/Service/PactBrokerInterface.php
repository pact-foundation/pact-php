<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Model\PactBroker\Matrix;

interface PactBrokerInterface
{
    public function publish(int $id): void;

    public function start(): void;

    public function stop(): void;

    public function getMatrix(): Matrix;
}
