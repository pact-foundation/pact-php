<?php

namespace PhpPact\Consumer\Driver\Interaction;

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
        $this->newInteraction($interaction);
        $this->given($interaction);
        $this->uponReceiving($interaction);
        $this->withRequest($interaction);
        $this->willRespondWith($interaction);
        $this->setKey($interaction);

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
        $handle = $this->client->call('pactffi_new_interaction', $this->pactDriver->getPact()->handle, $interaction->getDescription());
        $interaction->setHandle($handle);
    }

    private function uponReceiving(Interaction $interaction): void
    {
        $this->client->call('pactffi_upon_receiving', $interaction->getHandle(), $interaction->getDescription());
    }

    private function given(Interaction $interaction): void
    {
        foreach ($interaction->getProviderStates() as $providerState) {
            $this->client->call('pactffi_given', $interaction->getHandle(), $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $interaction->getHandle(), $providerState->getName(), (string) $key, (string) $value);
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
