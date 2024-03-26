<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

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
     * @param MatcherInterface|MatcherInterface[]|string[]|string $value
     *
     * @throws JsonException
     */
    public function addHeader(string $header, array|string|MatcherInterface $value): self
    {
        $this->headers[$header] = [];
        if (is_array($value)) {
            array_walk($value, fn (string|MatcherInterface $value) => $this->addHeaderValue($header, $value));
        } else {
            $this->addHeaderValue($header, $value);
        }

        return $this;
    }

    /**
     * @throws JsonException
     */
    private function addHeaderValue(string $header, string|MatcherInterface $value): void
    {
        $this->headers[$header][] = is_string($value) ? $value : json_encode($value, JSON_THROW_ON_ERROR);
    }
}
