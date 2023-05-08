<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\Interaction\Part\RequestDriverInterface;
use PhpPact\Consumer\Driver\Interaction\Part\ResponseDriverInterface;
use PhpPact\Consumer\Model\Interaction;

class InteractionRegistry implements InteractionRegistryInterface
{
    public function __construct(
        private InteractionDriverInterface $interactionDriver,
        private RequestDriverInterface $requestDriver,
        private ResponseDriverInterface $responseDriver,
        private MockServerInterface $mockServer
    ) {
    }

    public function verifyInteractions(): bool
    {
        $matched = $this->mockServer->isMatched();

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this
            ->newInteraction($interaction)
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction)
            ->startMockServer();

        return true;
    }

    private function cleanUp(): void
    {
        $this->mockServer->cleanUp();
    }

    private function writePact(): void
    {
        $this->mockServer->writePact();
    }

    private function newInteraction(Interaction $interaction): self
    {
        $this->interactionDriver->newInteraction($interaction->getDescription());

        return $this;
    }

    private function given(Interaction $interaction): self
    {
        $this->interactionDriver->given($interaction->getProviderStates());

        return $this;
    }

    private function uponReceiving(Interaction $interaction): self
    {
        $this->interactionDriver->uponReceiving($interaction->getDescription());

        return $this;
    }

    private function with(Interaction $interaction): self
    {
        $request = $interaction->getRequest();
        $this->requestDriver->withRequest($request->getMethod(), $request->getPath());
        $this->requestDriver->withHeaders($request->getHeaders());
        $this->requestDriver->withQueryParameters($request->getQuery());
        $this->requestDriver->withBody(null, $request->getBody());

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $response = $interaction->getResponse();
        $this->responseDriver->withResponse($response->getStatus());
        $this->responseDriver->withHeaders($response->getHeaders());
        $this->responseDriver->withBody(null, $response->getBody());

        return $this;
    }

    private function startMockServer(): void
    {
        $this->mockServer->start();
    }
}
