<?php

namespace PhpPact\SyncMessage\Driver\Interaction;

use PhpPact\Consumer\Driver\Exception\InteractionNotModifiedException;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Driver\Body\SyncMessageBodyDriver;
use PhpPact\SyncMessage\Driver\Body\SyncMessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Interaction\AbstractDriver;
use PhpPact\SyncMessage\Model\SyncMessage;

class SyncMessageDriver extends AbstractDriver implements SyncMessageDriverInterface
{
    private SyncMessageBodyDriverInterface $messageBodyDriver;

    public function __construct(
        private readonly MockServerInterface $mockServer,
        ClientInterface $client,
        private PactDriverInterface $pactDriver,
        ?SyncMessageBodyDriverInterface $messageBodyDriver = null
    ) {
        parent::__construct($client);
        $this->messageBodyDriver = $messageBodyDriver ?? new SyncMessageBodyDriver($client);
    }

    public function verifyMessage(): VerifyResult
    {
        return $this->mockServer->verify();
    }

    public function registerMessage(SyncMessage $message): void
    {
        $this->pactDriver->setUp();
        $this->newInteraction($message);
        $this->given($message);
        $this->expectsToReceive($message);
        $this->withMetadata($message);
        $this->withContents($message);
        $this->withMatchingRules($message);
        $this->withGenerators($message);
        $this->setKey($message);
        $this->setPending($message);
        $this->setComments($message);

        $this->mockServer->start();
    }

    public function writePactAndCleanUp(): void
    {
        $this->mockServer->writePact();
        $this->mockServer->cleanUp();
    }

    protected function newInteraction(SyncMessage $message): void
    {
        $handle = $this->client->newSyncMessageInteraction($this->pactDriver->getPact()->handle, $message->getDescription());
        $message->setHandle($handle);
    }

    protected function given(SyncMessage $message): void
    {
        foreach ($message->getProviderStates() as $providerState) {
            $success = $this->client->given($message->getHandle(), $providerState->getName());
            if (!$success) {
                throw new InteractionNotModifiedException();
            }
            foreach ($providerState->getParams() as $key => $value) {
                $success = $this->client->givenWithParam($message->getHandle(), $providerState->getName(), (string) $key, (string) $value);
                if (!$success) {
                    throw new InteractionNotModifiedException();
                }
            }
        }
    }

    private function withContents(SyncMessage $message): void
    {
        $this->messageBodyDriver->registerBody($message);
    }

    private function expectsToReceive(SyncMessage $message): void
    {
        $this->client->messageExpectsToReceive($message->getHandle(), $message->getDescription());
    }

    private function withMetadata(SyncMessage $message): void
    {
        foreach ($message->getRequestMetadata() as $key => $value) {
            $this->client->withMetadata($message->getHandle(), (string) $key, (string) $value, $this->client->getInteractionPartRequest());
        }
        foreach ($message->getResponseMetadata() as $key => $value) {
            $this->client->withMetadata($message->getHandle(), (string) $key, (string) $value, $this->client->getInteractionPartResponse());
        }
    }

    private function withMatchingRules(SyncMessage $message): void
    {
        $requestRules = $message->getRequestMatchingRules();
        if ($requestRules !== null) {
            $this->client->withMatchingRules($message->getHandle(), $this->client->getInteractionPartRequest(), $requestRules);
        }
        $responseRules = $message->getResponseMatchingRules();
        if ($responseRules !== null) {
            $this->client->withMatchingRules($message->getHandle(), $this->client->getInteractionPartResponse(), $responseRules);
        }
    }

    private function withGenerators(SyncMessage $message): void
    {
        $requestGenerators = $message->getRequestGenerators();
        if ($requestGenerators !== null) {
            $this->client->withGenerators($message->getHandle(), $this->client->getInteractionPartRequest(), $requestGenerators);
        }
        $responseGenerators = $message->getResponseGenerators();
        if ($responseGenerators !== null) {
            $this->client->withGenerators($message->getHandle(), $this->client->getInteractionPartResponse(), $responseGenerators);
        }
    }
}
