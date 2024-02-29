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
        $this->newInteraction($message);
        $this->given($message);
        $this->expectsToReceive($message);
        $this->withMetadata($message);
        $this->withContents($message);
        $this->setKey($message);
    }

    public function writePactAndCleanUp(): void
    {
        $this->pactDriver->writePact();
        $this->pactDriver->cleanUp();
    }

    protected function newInteraction(Message $message): void
    {
        $handle = $this->client->call('pactffi_new_message_interaction', $this->pactDriver->getPact()->handle, $message->getDescription());
        $message->setHandle($handle);
    }

    private function withContents(Message $message): void
    {
        $this->messageBodyDriver->registerBody($message);
    }

    private function expectsToReceive(Message $message): void
    {
        $this->client->call('pactffi_message_expects_to_receive', $message->getHandle(), $message->getDescription());
    }

    protected function given(Message $message): void
    {
        foreach ($message->getProviderStates() as $providerState) {
            $this->client->call('pactffi_message_given', $message->getHandle(), $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_message_given_with_param', $message->getHandle(), $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }

    private function withMetadata(Message $message): void
    {
        foreach ($message->getMetadata() as $key => $value) {
            $this->client->call('pactffi_message_with_metadata_v2', $message->getHandle(), (string) $key, (string) $value);
        }
    }
}
