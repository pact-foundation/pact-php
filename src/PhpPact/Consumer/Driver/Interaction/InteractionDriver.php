<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Exception\InteractionNotModifiedException;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\Model\VerifyResult;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    private RequestDriverInterface $requestDriver;
    private ResponseDriverInterface $responseDriver;

    public function __construct(
        ClientInterface $client,
        private MockServerInterface $mockServer,
        private PactDriverInterface $pactDriver,
        ?RequestDriverInterface $requestDriver = null,
        ?ResponseDriverInterface $responseDriver = null,
    ) {
        parent::__construct($client);
        $this->requestDriver = $requestDriver ?? new RequestDriver($client);
        $this->responseDriver = $responseDriver ?? new ResponseDriver($client);
    }

    public function verifyInteractions(): VerifyResult
    {
        return $this->mockServer->verify();
    }

    public function registerInteraction(Interaction $interaction, bool $startMockServer = true): bool
    {
        $this->pactDriver->setUp();
        $this->newInteraction($interaction);
        $this->given($interaction);
        $this->uponReceiving($interaction);
        $this->withRequest($interaction);
        $this->willRespondWith($interaction);
        $this->setKey($interaction);
        $this->setPending($interaction);
        $this->setComments($interaction);

        if ($startMockServer) {
            $this->mockServer->start();
        }

        return true;
    }

    public function writePactAndCleanUp(): void
    {
        $this->mockServer->writePact();
        $this->mockServer->cleanUp();
    }

    protected function newInteraction(Interaction $interaction): void
    {
        $handle = $this->client->newInteraction($this->pactDriver->getPact()->handle, $interaction->getDescription());
        $interaction->setHandle($handle);
    }

    private function uponReceiving(Interaction $interaction): void
    {
        $success = $this->client->uponReceiving($interaction->getHandle(), $interaction->getDescription());
        if (!$success) {
            throw new InteractionNotModifiedException();
        }
    }

    private function given(Interaction $interaction): void
    {
        foreach ($interaction->getProviderStates() as $providerState) {
            $success = $this->client->given($interaction->getHandle(), $providerState->getName());
            if (!$success) {
                throw new InteractionNotModifiedException();
            }
            foreach ($providerState->getParams() as $key => $value) {
                $success = $this->client->givenWithParam($interaction->getHandle(), $providerState->getName(), (string) $key, (string) $value);
                if (!$success) {
                    throw new InteractionNotModifiedException();
                }
            }
        }
    }

    private function withRequest(Interaction $interaction): void
    {
        $this->requestDriver->registerRequest($interaction);
    }

    private function willRespondWith(Interaction $interaction): void
    {
        $this->responseDriver->registerResponse($interaction);
    }
}
