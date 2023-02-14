<?php

namespace PhpPact\Consumer\Model;

/**
 * Request initiated by the consumer.
 */
class ConsumerRequest implements \JsonSerializable
{
    private string $method;

    private mixed $path;

    /**
     * @var array<string, string>
     */
    private array $headers;

    private mixed $body = null;

    private ?string $query = null;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPath(): mixed
    {
        return $this->path;
    }

    public function setPath(mixed $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header, string $value): self
    {
        $this->headers[$header] = $value;

        return $this;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function setBody(mixed $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return ConsumerRequest
     */
    public function addQueryParameter(string $key, string $value): self
    {
        if ($this->query === null) {
            $this->query = "{$key}={$value}";
        } else {
            $this->query = "{$this->query}&{$key}={$value}";
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $results = [];

        $results['method'] = $this->getMethod();

        if ($this->getHeaders() !== null) {
            $results['headers'] = $this->getHeaders();
        }

        if ($this->getPath() !== null) {
            $results['path'] = $this->getPath();
        }

        if ($this->getBody() !== null) {
            $results['body'] = $this->getBody();
        }

        if ($this->getQuery() !== null) {
            $results['query'] = $this->getQuery();
        }

        return $results;
    }
}
