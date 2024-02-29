<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\FFI\ClientInterface;

class MessageDriver implements MessageDriverInterface
{
    public function __construct(
        private ClientInterface $client,
        private PactDriverInterface $pactDriver,
        private MessageRegistryInterface $messageRegistry
    ) {
    }

    public function reify(): string
    {
        return $this->client->call('pactffi_message_reify', $this->messageRegistry->getId());
    }

    public function writePactAndCleanUp(): bool
    {
        $this->pactDriver->writePact();
        $this->pactDriver->cleanUp();

        return true;
    }

    public function registerMessage(Message $message): void
    {
        $this->messageRegistry->registerMessage($message);
    }
}
