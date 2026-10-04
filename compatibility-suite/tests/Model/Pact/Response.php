<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

final class Response
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getStatus(): int
    {
        return TypeCaster::toInt($this->data['status'] ?? 0);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getHeaders(): array
    {
        return (array) ($this->data['headers'] ?? []);
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
