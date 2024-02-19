<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriver;
use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\FFI\ClientInterface;

abstract class AbstractMessageDriver extends AbstractDriver
{
    private MessageBodyDriverInterface $messageBodyDriver;

    public function __construct(
        protected ClientInterface $client,
        PactDriverInterface $pactDriver,
        ?MessageBodyDriverInterface $messageBodyDriver = null
    ) {
        $this->messageBodyDriver = $messageBodyDriver ?? new MessageBodyDriver($client);
        parent::__construct($pactDriver);
    }

    public function registerMessage(Message $message): void
    {
        $this->validatePact();
        $this->newInteraction($message, $this->pact);
        $this->given($message);
        $this->expectsToReceive($message);
        $this->withMetadata($message);
        $this->withContents($message);
    }

    protected function newInteraction(Message $message, Pact $pact): void
    {
        $id = $this->client->call('pactffi_new_message_interaction', $pact->handle, $message->getDescription());
        $message->setHandle($id);
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
