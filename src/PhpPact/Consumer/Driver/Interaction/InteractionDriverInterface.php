<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;

interface InteractionDriverInterface extends DriverInterface
{
    public function uponReceiving(string $description): self;

    /**
     * @param ProviderState[] $providerStates
     */
    public function given(array $providerStates): self;

    public function with(ConsumerRequest $request): self;

    public function willRespondWith(ProviderResponse $response): self;
}
