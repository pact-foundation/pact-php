<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Mock Server for PACT tests.
 */
class Interaction
{
    use ProviderStates;

    private string $description;

    private ConsumerRequest $request;

    private ProviderResponse $response;

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequest(): ConsumerRequest
    {
        return $this->request;
    }

    public function setRequest(ConsumerRequest $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): ProviderResponse
    {
        return $this->response;
    }

    public function setResponse(ProviderResponse $response): self
    {
        $this->response = $response;

        return $this;
    }
}
