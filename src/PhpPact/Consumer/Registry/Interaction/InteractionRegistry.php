<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Registry\Interaction\Part\RequestRegistry;
use PhpPact\Consumer\Registry\Interaction\Part\RequestRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Part\ResponseRegistry;
use PhpPact\Consumer\Registry\Interaction\Part\ResponseRegistryInterface;
use PhpPact\Consumer\Registry\Pact\PactRegistryInterface;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Consumer\Model\ProviderState;
use PhpPact\FFI\ClientInterface;

class InteractionRegistry extends AbstractRegistry implements InteractionRegistryInterface
{
    private RequestRegistryInterface $requestRegistry;
    private ResponseRegistryInterface $responseRegistry;

    public function __construct(
        ClientInterface $client,
        PactRegistryInterface $pactRegistry,
        ?RequestRegistryInterface $requestRegistry = null,
        ?ResponseRegistryInterface $responseRegistry = null,
    ) {
        parent::__construct($client, $pactRegistry);
        $this->requestRegistry = $requestRegistry ?? new RequestRegistry($client, $this);
        $this->responseRegistry = $responseRegistry ?? new ResponseRegistry($client, $this);
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this
            ->newInteraction($interaction->getDescription())
            ->given($interaction->getProviderStates())
            ->uponReceiving($interaction->getDescription())
            ->with($interaction->getRequest())
            ->willRespondWith($interaction->getResponse());

        return true;
    }

    protected function newInteraction(string $description): self
    {
        $this->id = $this->client->call('pactffi_new_interaction', $this->pactRegistry->getId(), $description);

        return $this;
    }

    private function uponReceiving(string $description): self
    {
        $this->client->call('pactffi_upon_receiving', $this->id, $description);

        return $this;
    }

    /**
     * @param ProviderState[] $providerStates
     */
    private function given(array $providerStates): self
    {
        foreach ($providerStates as $providerState) {
            $this->client->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->client->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }

        return $this;
    }

    private function with(ConsumerRequest $request): self
    {
        $this->requestRegistry
            ->withRequest($request->getMethod(), $request->getPath())
            ->withQueryParameters($request->getQuery())
            ->withHeaders($request->getHeaders())
            ->withBody(null, $request->getBody());

        return $this;
    }

    private function willRespondWith(ProviderResponse $response): self
    {
        $this->responseRegistry
            ->withResponse($response->getStatus())
            ->withHeaders($response->getHeaders())
            ->withBody(null, $response->getBody());

        return $this;
    }
}
