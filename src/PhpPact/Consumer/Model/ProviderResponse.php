<?php

namespace PhpPact\Consumer\Model;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 * Class ProviderResponse.
 */
class ProviderResponse implements \JsonSerializable
{
    /**
     * @var int
     */
    private int $status;

    /**
     * @var string[]
     */
    private array $headers = [];

    /**
     * @var null|string
     */
    private ?string $body  = null;

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
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     *
     * @return ProviderResponse
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
     * @return ProviderResponse
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
     * @return ProviderResponse
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
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        $results = [
            'status' => $this->getStatus(),
        ];

        if ($this->getHeaders() !== null) {
            $results['headers'] = $this->getHeaders();
        }

        if ($this->getBody() !== null) {
            $results['body'] = $this->getBody();
        }

        return $results;
    }
}
