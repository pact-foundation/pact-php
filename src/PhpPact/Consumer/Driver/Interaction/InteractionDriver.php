<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\InteractionPart\RequestDriver;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Model\VerifyResult;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    private RequestDriverInterface $requestDriver;
    private ResponseDriverInterface $responseDriver;

    public function __construct(
        private ClientInterface $client,
        private MockServerInterface $mockServer,
        PactDriverInterface $pactDriver,
        ?RequestDriverInterface $requestDriver = null,
        ?ResponseDriverInterface $responseDriver = null,
    ) {
        $this->requestDriver = $requestDriver ?? new RequestDriver($client);
        $this->responseDriver = $responseDriver ?? new ResponseDriver($client);
        parent::__construct($pactDriver);
    }

    public function verifyInteractions(): VerifyResult
    {
        $this->validatePact();
        $result = $this->mockServer->verify();
        $this->deletePact();

        return $result;
    }

    public function registerInteraction(Interaction $interaction, bool $startMockServer = true): bool
    {
        $this->validatePact();
        $this->newInteraction($interaction, $this->pact);
        $this->given($interaction);
        $this->uponReceiving($interaction);
        $this->withRequest($interaction);
        $this->willRespondWith($interaction);

        if ($startMockServer) {
            $this->mockServer->start($this->pact);
        }

        return true;
    }

    protected function writePact(): void
    {
        $this->mockServer->writePact();
    }

    protected function newInteraction(Interaction $interaction, Pact $pact): void
    {
        $id = $this->client->call('pactffi_new_interaction', $pact->handle, $interaction->getDescription());
        $interaction->setHandle($id);
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
