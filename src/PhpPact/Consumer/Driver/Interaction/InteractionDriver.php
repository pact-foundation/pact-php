<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\Consumer\Service\MockServerInterface;

class InteractionDriver implements InteractionDriverInterface
{
    public function __construct(
        private PactDriverInterface $pactDriver,
        private InteractionRegistryInterface $interactionRegistry,
        private MockServerInterface $mockServer,
    ) {
    }

    public function verifyInteractions(): bool
    {
        return $this->mockServer->verify();
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this->pactDriver->setUp();
        $this->interactionRegistry->registerInteraction($interaction);
        $this->mockServer->start();

        return true;
    }
}
