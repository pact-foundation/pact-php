<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 */
class Interaction implements \JsonSerializable
{
    private string $description;

    private ?string $providerState = null;

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

    public function getProviderState(): ?string
    {
        return $this->providerState;
    }

    public function setProviderState(string $providerState): self
    {
        $this->providerState = $providerState;

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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        if ($this->getProviderState()) {
            return [
                'description'   => $this->getDescription(),
                'providerState' => $this->getProviderState(),
                'request'       => $this->getRequest(),
                'response'      => $this->getResponse(),
            ];
        }

        return [
                'description'   => $this->getDescription(),
                'request'       => $this->getRequest(),
                'response'      => $this->getResponse(),
            ];
    }
}
