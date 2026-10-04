<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

final class Request
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getMethod(): string
    {
        return TypeCaster::toString($this->data['method'] ?? '');
    }

    public function getPath(): string
    {
        return TypeCaster::toString($this->data['path'] ?? '');
    }

    /**
     * @return mixed a query string (V1/V2) or a query parameter map (V3+)
     */
    public function getQuery(): mixed
    {
        return $this->data['query'] ?? null;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getHeaders(): array
    {
        return (array) ($this->data['headers'] ?? []);
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->getHeaders());
    }

    public function getHeader(string $name): string
    {
        return TypeCaster::toString($this->getHeaders()[$name] ?? '');
    }

    /**
     * @return mixed the raw (decoded JSON) body
     */
    public function getBody(): mixed
    {
        return $this->data['body'] ?? null;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getMetadata(): array
    {
        return (array) ($this->data['metadata'] ?? []);
    }

    public function getContents(): ?Contents
    {
        if (!isset($this->data['contents'])) {
            return null;
        }

        return new Contents((array) $this->data['contents']);
    }
}
