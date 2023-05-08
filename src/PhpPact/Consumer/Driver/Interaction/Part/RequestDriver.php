<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

class RequestDriver extends AbstractPartDriver implements RequestDriverInterface
{
    public function withQueryParameters(array $queryParams): void
    {
        foreach ($queryParams as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->client->call('pactffi_with_query_parameter_v2', $this->getInteractionId(), (string) $key, (int) $index, (string) $value);
            }
        }
    }

    public function withRequest(string $method, string $path): void
    {
        $this->client->call('pactffi_with_request', $this->getInteractionId(), $method, $path);
    }

    protected function getPart(): int
    {
        return $this->client->get('InteractionPart_Request');
    }
}
