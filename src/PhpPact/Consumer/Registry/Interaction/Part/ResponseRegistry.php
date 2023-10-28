<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

use PhpPact\Consumer\Registry\Interaction\Body\BodyRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Body\ResponseBodyRegistry;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

class ResponseRegistry extends AbstractPartRegistry implements ResponseRegistryInterface
{
    use ResponsePartTrait;

    public function __construct(
        ClientInterface $client,
        InteractionRegistryInterface $interactionRegistry,
        ?BodyRegistryInterface $responseBodyRegistry = null
    ) {
        parent::__construct($client, $interactionRegistry, $responseBodyRegistry ?? new ResponseBodyRegistry($client, $interactionRegistry));
    }

    public function withResponse(string $status): self
    {
        $this->client->call('pactffi_response_status_v2', $this->getInteractionId(), $status);

        return $this;
    }
}
