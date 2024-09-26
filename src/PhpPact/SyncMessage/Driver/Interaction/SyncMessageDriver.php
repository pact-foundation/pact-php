<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Interaction\AbstractMessageDriver;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;

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

    public function verifyMessage(): VerifyResult
    {
        return $this->mockServer->verify();
    }

    public function registerMessage(Message $message): void
    {
        parent::registerMessage($message);

        $this->mockServer->start();
    }

    public function writePactAndCleanUp(): void
    {
        $this->mockServer->writePact();
        $this->mockServer->cleanUp();
    }

    protected function newInteraction(Message $message): void
    {
        $handle = $this->client->newSyncMessageInteraction($this->pactDriver->getPact()->handle, $message->getDescription());
        $message->setHandle($handle);
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
}
