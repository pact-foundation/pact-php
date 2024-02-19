<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Model\Interaction\DescriptionTrait;
use PhpPact\Consumer\Model\Interaction\IdTrait;

/**
 * Request/Response Pair to be posted to the Mock Server for PACT tests.
 */
class Interaction
{
    use ProviderStates;
    use DescriptionTrait;
    use IdTrait;

    private ConsumerRequest $request;

    private ProviderResponse $response;

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
