<?php

namespace PhpPact\Consumer\Model;

/**
 * Request initiated by the consumer.
 * Class ConsumerRequest.
 */
class ConsumerRequest
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var array<string, string[]>
     */
    private array $headers = [];

    /**
     * @var null|string
     */
    private ?string $body  = null;

    /**
     * @var array<string, string[]>
     */
    private array $query = [];

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return ConsumerRequest
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param array|string $path
     *
     * @return ConsumerRequest
     */
    public function setPath(array|string $path): self
    {
        $this->path = is_array($path) ? json_encode($path) : $path;

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
     * @param string[] $headers
     *
     * @return ConsumerRequest
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
     * @param string       $header
     * @param array|string $value
     *
     * @return ConsumerRequest
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

    /**
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     *
     * @return ConsumerRequest
     */
    public function setBody(mixed $body): self
    {
        if (\is_string($body)) {
            $this->body = $body;
        } elseif (!\is_null($body)) {
            $this->body = \json_encode($body);
            $this->addHeader('Content-Type', 'application/json');
        } else {
            $this->body = null;
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
     * @param array $query
     *
     * @return ConsumerRequest
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
     * @param string       $key
     * @param array|string $value
     *
     * @return ConsumerRequest
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
