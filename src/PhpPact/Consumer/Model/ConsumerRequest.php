<?php

namespace PhpPact\Consumer\Model;

/**
 * Request initiated by the consumer.
 * Class ConsumerRequest.
 */
class ConsumerRequest implements \JsonSerializable
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
     * @var string[]
     */
    private array $headers = [];

    /**
     * @var null|string
     */
    private ?string $body  = null;

    /**
     * @var array
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
     * @return array
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
        $this->headers = $headers;

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
        $this->headers[$header] = is_array($value) ? json_encode($value) : $value;

        return $this;
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
     * @return array
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
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $key
     * @param array|string $value
     *
     * @return ConsumerRequest
     */
    public function addQueryParameter(string $key, array|string $value): self
    {
        $this->query[$key] = is_array($value) ? json_encode($value) : $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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
