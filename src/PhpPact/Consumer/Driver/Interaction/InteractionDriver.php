<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    public function newInteraction(string $description): void
    {
        $this->id = $this->ffi->call('pactffi_new_interaction', $this->pactDriver->getId(), $description);
    }

    public function uponReceiving(string $description): void
    {
        $this->ffi->call('pactffi_upon_receiving', $this->id, $description);
    }

    public function withBody(string $part, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->ffi->call('pactffi_with_body', $this->id, $this->ffi->get($part), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function given(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->ffi->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->ffi->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withHeaders(string $part, array $headers): void
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
    public function withQueryParameters(array $queryParams): void
    {
        foreach ($queryParams as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->ffi->call('pactffi_with_query_parameter_v2', $this->id, (string) $key, (int) $index, (string) $value);
            }
        }
    }

    public function withRequest(string $method, string $path): void
    {
        $this->ffi->call('pactffi_with_request', $this->id, $method, $path);
    }

    public function withResponse(int $status): void
    {
        $this->ffi->call('pactffi_response_status', $this->id, $status);
    }
}
