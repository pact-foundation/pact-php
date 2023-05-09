<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Driver\Interaction\Part\RequestDriver;
use PhpPact\Consumer\Driver\Interaction\Part\RequestDriverInterface;
use PhpPact\Consumer\Driver\Interaction\Part\ResponseDriver;
use PhpPact\Consumer\Driver\Interaction\Part\ResponseDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\FFI\ClientInterface;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    private RequestDriverInterface $requestDriver;
    private ResponseDriverInterface $responseDriver;

    public function __construct(
        ClientInterface $client,
        PactDriverInterface $pactDriver,
        ?RequestDriverInterface $requestDriver = null,
        ?ResponseDriverInterface $responseDriver = null,
    ) {
        parent::__construct($client, $pactDriver);
        $this->requestDriver = $requestDriver ?? new RequestDriver($client, $this);
        $this->responseDriver = $responseDriver ?? new ResponseDriver($client, $this);
    }

    public function newInteraction(string $description): static
    {
        $this->id = $this->client->call('pactffi_new_interaction', $this->pactDriver->getId(), $description);

        return $this;
    }

    public function uponReceiving(string $description): self
    {
        $this->client->call('pactffi_upon_receiving', $this->id, $description);

        return $this;
    }

    public function given(array $providerStates): self
    {
        foreach ($providerStates as $providerState) {
            $this->client->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }

        return $this;
    }

    public function with(ConsumerRequest $request): self
    {
        $this->requestDriver
            ->withRequest($request->getMethod(), $request->getPath())
            ->withQueryParameters($request->getQuery())
            ->withHeaders($request->getHeaders())
            ->withBody(null, $request->getBody());

        return $this;
    }

    public function willRespondWith(ProviderResponse $response): self
    {
        $this->responseDriver
            ->withResponse($response->getStatus())
            ->withHeaders($response->getHeaders())
            ->withBody(null, $response->getBody());

        return $this;
    }
}
