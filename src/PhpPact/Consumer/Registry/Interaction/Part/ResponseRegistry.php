<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

use PhpPact\Consumer\Registry\Interaction\Contents\ContentsRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Contents\ResponseBodyRegistry;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

class ResponseRegistry extends AbstractPartRegistry implements ResponseRegistryInterface
{
    use ResponsePartTrait;

    public function __construct(
        ClientInterface $client,
        InteractionRegistryInterface $interactionRegistry,
        ?ContentsRegistryInterface $responseBodyRegistry = null
    ) {
        parent::__construct($client, $interactionRegistry, $responseBodyRegistry ?? new ResponseBodyRegistry($client, $interactionRegistry));
    }

    public function withResponse(int $status): self
    {
        $this->client->call('pactffi_response_status', $this->getInteractionId(), $status);

        return $this;
    }
}
