<?php

namespace PhpPact\Consumer\Model;

/**
 * Request initiated by the consumer.
 * Class ConsumerRequest.
 */
class ConsumerRequest
{
    private string $method;
    private string $path;
    private ?string $body  = null;
    private array $headers = [];
    private array $query   = [];

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
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return ConsumerRequest
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = [];
        foreach ($headers as $header => $values) {
            $this->addHeader($header, $values);
        }

        return $this;
    }

    /**
     * @param string       $header
     * @param array|string $values
     *
     * @return ConsumerRequest
     */
    public function addHeader(string $header, $values): self
    {
        $this->headers[$header] = [];
        if (\is_array($values)) {
            foreach ($values as $value) {
                $this->addHeaderValue($header, $value);
            }
        } else {
            $this->addHeaderValue($header, $values);
        }

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
    public function setBody($body): self
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
     * @param string $query
     *
     * @return ConsumerRequest
     */
    public function setQuery(string $query): self
    {
        foreach (\explode('&', $query) as $parameter) {
            $this->addQueryParameter(...\explode('=', $parameter));
        }

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
        $this->query[$key][] = $value;

        return $this;
    }

    /**
     * @param string $header
     * @param string $value
     */
    private function addHeaderValue(string $header, string $value): void
    {
        $this->headers[$header][] = $value;
    }
}
