<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriver;
use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\FFI\ClientInterface;

abstract class AbstractMessageDriver extends AbstractDriver implements SharedMessageDriverInterface
{
    private MessageBodyDriverInterface $messageBodyDriver;

    public function __construct(
        ClientInterface $client,
        protected PactDriverInterface $pactDriver,
        ?MessageBodyDriverInterface $messageBodyDriver = null
    ) {
        parent::__construct($client);
        $this->messageBodyDriver = $messageBodyDriver ?? new MessageBodyDriver($client);
    }

    public function registerMessage(Message $message): void
    {
        $this->pactDriver->setUp();
        $this->newInteraction($message);
        $this->given($message);
        $this->expectsToReceive($message);
        $this->withMetadata($message);
        $this->withContents($message);
        $this->setKey($message);
        $this->setPending($message);
        $this->setComments($message);
    }

    public function writePactAndCleanUp(): void
    {
        $this->pactDriver->writePact();
        $this->pactDriver->cleanUp();
    }

    protected function newInteraction(Message $message): void
    {
        $handle = $this->client->newMessageInteraction($this->pactDriver->getPact()->handle, $message->getDescription());
        $message->setHandle($handle);
    }

    private function withContents(Message $message): void
    {
        $this->messageBodyDriver->registerBody($message);
    }

    private function expectsToReceive(Message $message): void
    {
        $this->client->messageExpectsToReceive($message->getHandle(), $message->getDescription());
    }

    protected function given(Message $message): void
    {
        foreach ($message->getProviderStates() as $providerState) {
            $this->client->messageGiven($message->getHandle(), $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->messageGivenWithParam($message->getHandle(), $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }

    private function withMetadata(Message $message): void
    {
        foreach ($message->getMetadata() as $key => $value) {
            $this->client->messageWithMetadataV2($message->getHandle(), (string) $key, (string) $value);
        }
    }
}
