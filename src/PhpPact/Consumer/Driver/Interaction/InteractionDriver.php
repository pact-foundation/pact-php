<?php

namespace PhpPact\Consumer\Driver\Interaction;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    public function newInteraction(string $description): void
    {
        $this->id = $this->ffi->call('pactffi_new_interaction', $this->pactDriver->getId(), $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(string $part, array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->call('pactffi_with_header_v2', $this->id, $this->ffi->get($part), (string) $header, (int) $index, (string) $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery(array $query): void
    {
        foreach ($query as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->call('pactffi_with_query_parameter_v2', $this->id, (string) $key, (int) $index, (string) $value);
            }
        }
    }

    public function setRequest(string $method, string $path): void
    {
        $this->ffi->call('pactffi_with_request', $this->id, $method, $path);
    }

    public function setResponse(int $status): void
    {
        $this->ffi->call('pactffi_response_status', $this->id, $status);
    }
}
