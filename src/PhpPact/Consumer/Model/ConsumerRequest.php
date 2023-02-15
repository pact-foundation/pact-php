<?php

namespace PhpPact\Consumer\Model;

/**
 * Request initiated by the consumer.
 */
class ConsumerRequest implements \JsonSerializable
{
    private string $method;

    /**
     * @var string|array<string, mixed>
     */
    private string|array $path;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

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

    /**
     * @return string|array<string, mixed>
     */
    public function getPath(): string|array
    {
        return $this->path;
    }

    /**
     * @param string|array<string, mixed> $path
     */
    public function setPath(string|array $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array<string, string>
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
    public function jsonSerialize(): array
    {
        $results = [];

        $results['method'] = $this->getMethod();

        if (count($this->getHeaders()) > 0) {
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
