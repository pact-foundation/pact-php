<?php

namespace PhpPact\Consumer\Model;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 * Class ProviderResponse.
 */
class ProviderResponse
{
    private int $status;
    private ?string $body  = null;
    private array $headers = [];

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return ProviderResponse
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

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
     * @return ProviderResponse
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
     * @return ProviderResponse
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
     * @return ProviderResponse
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
     * @param string $header
     * @param string $value
     */
    private function addHeaderValue(string $header, string $value): void
    {
        $this->headers[$header][] = $value;
    }
}
