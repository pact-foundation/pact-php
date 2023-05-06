<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Exception\InteractionBodyNotAddedException;

class InteractionDriver extends AbstractDriver implements InteractionDriverInterface
{
    private const REQUEST = 'InteractionPart_Request';
    private const RESPONSE = 'InteractionPart_Response';

    public function newInteraction(string $description): void
    {
        $this->id = $this->proxy->call('pactffi_new_interaction', $this->pactDriver->getId(), $description);
    }

    public function uponReceiving(string $description): void
    {
        $this->proxy->call('pactffi_upon_receiving', $this->id, $description);
    }

    public function withBody(bool $isRequest, ?string $contentType = null, ?string $body = null): void
    {
        if (is_null($body)) {
            return;
        }
        $success = $this->proxy->call('pactffi_with_body', $this->id, $this->getPart($isRequest), $contentType, $body);
        if (!$success) {
            throw new InteractionBodyNotAddedException();
        }
    }

    public function given(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->proxy->call('pactffi_given', $this->id, $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->proxy->call('pactffi_given_with_param', $this->id, $providerState->getName(), (string) $key, (string) $value);
            }
        }
    }

    public function withHeaders(bool $isRequest, array $headers): void
    {
        foreach ($headers as $header => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->proxy->call('pactffi_with_header_v2', $this->id, $this->getPart($isRequest), (string) $header, (int) $index, (string) $value);
            }
        }
    }

    public function withQueryParameters(array $queryParams): void
    {
        foreach ($queryParams as $key => $values) {
            foreach (array_values($values) as $index => $value) {
                $this->proxy->call('pactffi_with_query_parameter_v2', $this->id, (string) $key, (int) $index, (string) $value);
            }
        }
    }

    public function withRequest(string $method, string $path): void
    {
        $this->proxy->call('pactffi_with_request', $this->id, $method, $path);
    }

    public function withResponse(int $status): void
    {
        $this->proxy->call('pactffi_response_status', $this->id, $status);
    }

    private function getPart(bool $isRequest): int
    {
        return $this->proxy->get($isRequest ? self::REQUEST : self::RESPONSE);
    }
}
