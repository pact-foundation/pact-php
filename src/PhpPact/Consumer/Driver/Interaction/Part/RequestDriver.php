<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

use PhpPact\Consumer\Driver\Interaction\Contents\ContentsDriverInterface;
use PhpPact\Consumer\Driver\Interaction\Contents\RequestBodyDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\FFI\ClientInterface;

class RequestDriver extends AbstractPartDriver implements RequestDriverInterface
{
    use RequestPartTrait;

    public function __construct(
        ClientInterface $client,
        InteractionDriverInterface $interactionDriver,
        ?ContentsDriverInterface $requestBodyDriver = null
    ) {
        parent::__construct($client, $interactionDriver, $requestBodyDriver ?? new RequestBodyDriver($client, $interactionDriver));
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
