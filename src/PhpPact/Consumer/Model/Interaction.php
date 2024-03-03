<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\Interaction\CommentsTrait;
use PhpPact\Consumer\Model\Interaction\DescriptionTrait;
use PhpPact\Consumer\Model\Interaction\HandleTrait;
use PhpPact\Consumer\Model\Interaction\KeyTrait;
use PhpPact\Consumer\Model\Interaction\PendingTrait;

/**
 * Request/Response Pair to be posted to the Mock Server for PACT tests.
 */
class Interaction
{
    use ProviderStates;
    use DescriptionTrait;
    use HandleTrait;
    use KeyTrait;
    use PendingTrait;
    use CommentsTrait;

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

    public function getBody(InteractionPart $part): Text|Binary|Multipart|null
    {
        return match ($part) {
            InteractionPart::REQUEST => $this->getRequest()->getBody(),
            InteractionPart::RESPONSE => $this->getResponse()->getBody(),
        };
    }

    /**
     * @return array<string, string[]>
     */
    public function getHeaders(InteractionPart $part): array
    {
        return match ($part) {
            InteractionPart::REQUEST => $this->getRequest()->getHeaders(),
            InteractionPart::RESPONSE => $this->getResponse()->getHeaders(),
        };
    }
}
