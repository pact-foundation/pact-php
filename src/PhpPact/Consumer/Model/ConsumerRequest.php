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
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var mixed
     */
    private $body;

    /**
     * @var string
     */
    private $query;

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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return ConsumerRequest
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
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
    public function addHeader(string $header, $value): self
    {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     *
     * @return ConsumerRequest
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     *
     * @return ConsumerRequest
     */
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
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
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
