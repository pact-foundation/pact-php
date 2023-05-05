<?php

namespace PhpPact\Consumer\Model;

use JsonException;

/**
 * Request initiated by the consumer.
 */
class ConsumerRequest
{
    private string $method;

    private string $path;

    /**
     * @var array<string, string[]>
     */
    private array $headers = [];

    private ?string $body  = null;

    /**
     * @var array<string, string[]>
     */
    private array $query = [];

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string|array<string, mixed> $path
     *
     * @throws JsonException
     */
    public function setPath(string|array $path): self
    {
        $this->path = is_array($path) ? json_encode($path, JSON_THROW_ON_ERROR) : $path;

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
     * @param array<string, string|string[]> $headers
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
     * @param string|string[] $value
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
     * @throws JsonException
     */
    public function setBody(mixed $body): self
    {
        if (\is_string($body) || \is_null($body)) {
            $this->body = $body;
        } else {
            $this->body = \json_encode($body, JSON_THROW_ON_ERROR);
            $this->addHeader('Content-Type', 'application/json');
        }

        return $this;
    }

    /**
     * @return array<string, string[]>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array<string, string|string[]> $query
     */
    public function setQuery(array $query): self
    {
        $this->query = [];
        foreach ($query as $key => $value) {
            $this->addQueryParameter($key, $value);
        }

        return $this;
    }

    /**
     * @param string|string[] $value
     */
    public function addQueryParameter(string $key, array|string $value): self
    {
        $this->query[$key] = [];
        if (is_array($value)) {
            array_walk($value, fn (string $value) => $this->addQueryParameterValue($key, $value));
        } else {
            $this->addQueryParameterValue($key, $value);
        }

        return $this;
    }

    private function addQueryParameterValue(string $key, string $value): void
    {
        $this->query[$key][] = $value;
    }
}
