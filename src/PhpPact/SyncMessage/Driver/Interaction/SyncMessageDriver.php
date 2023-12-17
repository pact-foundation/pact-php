<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\Consumer\Service\MockServerInterface;

class SyncMessageDriver implements SyncMessageDriverInterface
{
    public function __construct(
        private PactDriverInterface $pactDriver,
        private MessageRegistryInterface $messageRegistry,
        private MockServerInterface $mockServer
    ) {
    }

    public function verifyMessage(): bool
    {
        return $this->mockServer->verify();
    }

    public function registerMessage(Message $message): void
    {
        $this->pactDriver->setUp();
        $this->messageRegistry->registerMessage($message);

        $this->mockServer->start();
    }
}
