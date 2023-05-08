<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\ProviderState;

interface InteractionDriverInterface extends DriverInterface
{
    public function uponReceiving(string $description): void;

    /**
     * @param ProviderState[] $providerStates
     */
    public function given(array $providerStates): void;
}
