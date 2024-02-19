<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Interaction\AbstractMessageDriver;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;

class SyncMessageDriver extends AbstractMessageDriver implements SyncMessageDriverInterface
{
    public function __construct(
        private MockServerInterface $mockServer,
        ClientInterface $client,
        PactDriverInterface $pactDriver,
        ?MessageBodyDriverInterface $messageBodyDriver = null
    ) {
        parent::__construct($client, $pactDriver, $messageBodyDriver);
    }

    public function verifyMessage(): bool
    {
        $this->validatePact();
        $result = $this->mockServer->verify();
        $this->deletePact();

        return $result->isSuccess();
    }

    public function registerMessage(Message $message): void
    {
        parent::registerMessage($message);

        $this->mockServer->start($this->pact);
    }

    protected function newInteraction(Message $message, Pact $pact): void
    {
        $id = $this->client->call('pactffi_new_sync_message_interaction', $pact->handle, $message->getDescription());
        $message->setHandle($id);
    }

    protected function given(Message $message): void
    {
        foreach ($message->getProviderStates() as $providerState) {
            $this->client->call('pactffi_given', $message->getHandle(), $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $message->getHandle(), $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }

    protected function writePact(): void
    {
        $this->mockServer->writePact();
    }
}
