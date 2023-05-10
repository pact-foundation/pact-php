<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

use PhpPact\Consumer\Registry\Interaction\Contents\ContentsRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Contents\RequestBodyRegistry;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistryInterface;
use PhpPact\FFI\ClientInterface;

class RequestRegistry extends AbstractPartRegistry implements RequestRegistryInterface
{
    use RequestPartTrait;

    public function __construct(
        ClientInterface $client,
        InteractionRegistryInterface $interactionRegistry,
        ?ContentsRegistryInterface $requestBodyRegistry = null
    ) {
        parent::__construct($client, $interactionRegistry, $requestBodyRegistry ?? new RequestBodyRegistry($client, $interactionRegistry));
    }

    public function withQueryParameters(array $queryParams): self
    {
        foreach ($queryParams as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_query_parameter_v2', $this->getInteractionId(), (string) $key, (int) $index, (string) $value);
            }
        }

        return $this;
    }

    public function withRequest(string $method, string $path): self
    {
        $this->client->call('pactffi_with_request', $this->getInteractionId(), $method, $path);

        return $this;
    }
}
