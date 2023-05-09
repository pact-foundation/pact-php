<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Model\Interaction;

class InteractionRegistry implements InteractionRegistryInterface
{
    public function __construct(
        private InteractionDriverInterface $interactionDriver,
        private MockServerInterface $mockServer
    ) {
    }

    public function verifyInteractions(): bool
    {
        return $this->mockServer->verify();
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this->interactionDriver
            ->newInteraction($interaction->getDescription())
            ->given($interaction->getProviderStates())
            ->uponReceiving($interaction->getDescription())
            ->with($interaction->getRequest())
            ->willRespondWith($interaction->getResponse());

        $this->mockServer->start();

        return true;
    }
}
