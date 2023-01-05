<?php

namespace PhpPact\Consumer\Model;

/**
 * Request/Response Pair to be posted to the Ruby Standalone Mock Server for PACT tests.
 * Class Interaction.
 */
class Interaction
{
    use ProviderStates;

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var ConsumerRequest
     */
    private ConsumerRequest $request;

    /**
     * @var ProviderResponse
     */
    private ProviderResponse $response;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Interaction
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Interaction
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ConsumerRequest
     */
    public function getRequest(): ConsumerRequest
    {
        return $this->request;
    }

    /**
     * @param ConsumerRequest $request
     *
     * @return Interaction
     */
    public function setRequest(ConsumerRequest $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return ProviderResponse
     */
    public function getResponse(): ProviderResponse
    {
        return $this->response;
    }

    /**
     * @param ProviderResponse $response
     *
     * @return Interaction
     */
    public function setResponse(ProviderResponse $response): self
    {
        $this->response = $response;

        return $this;
    }
}
