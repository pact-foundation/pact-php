<?php

namespace PhpPact\Consumer\Model;

use JsonException;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 */
class ProviderResponse
{
    private int $status;

    /**
     * @var array<string, string[]>
     */
    private array $headers = [];

    private ?string $body  = null;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array<string, string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<string, string[]> $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = [];
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }

        return $this;
    }

    /**
     * @param string[]|string $value
     */
    public function addHeader(string $header, array|string $value): self
    {
        $this->headers[$header] = [];
        if (is_array($value)) {
            array_walk($value, fn (string $value) => $this->addHeaderValue($header, $value));
        } else {
            $this->addHeaderValue($header, $value);
        }

        return $this;
    }

    private function addHeaderValue(string $header, string $value): void
    {
        $this->headers[$header][] = $value;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param array<mixed>|string|null $body
     *
     * @throws JsonException
     */
    public function setBody(array|string|null $body): self
    {
        if (\is_string($body) || \is_null($body)) {
            $this->body = $body;
        } else {
            $this->body = \json_encode($body, JSON_THROW_ON_ERROR);
            $this->addHeader('Content-Type', 'application/json');
        }

        return $this;
    }
}
