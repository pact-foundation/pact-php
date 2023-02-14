<?php

namespace PhpPact\Consumer\Model;

/**
 * Response expectation that would be in response to a Consumer request from the Provider.
 */
class ProviderResponse implements \JsonSerializable
{
    private int $status;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * @var ?array<mixed, mixed>
     */
    private ?array $body = null;

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
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<string, string> $headers
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

    /**
     * @return ?array<mixed, mixed>
     */
    public function getBody(): ?array
    {
        return $this->body;
    }

    /**
     * @param array<mixed, mixed> $body
     */
    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $results = [
            'status' => $this->getStatus(),
        ];

        if (count($this->getHeaders()) > 0) {
            $results['headers'] = $this->getHeaders();
        }

        if ($this->getBody() !== null) {
            $results['body'] = $this->getBody();
        }

        return $results;
    }
}
