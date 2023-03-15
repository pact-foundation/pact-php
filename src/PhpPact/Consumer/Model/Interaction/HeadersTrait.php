<?php

namespace PhpPact\Consumer\Model\Interaction;

trait HeadersTrait
{
    /**
     * @var array<string, string[]>
     */
    private array $headers = [];

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
     * @param string[]|string $value
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
}
