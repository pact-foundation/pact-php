<?php

namespace PhpPactTest\CompatibilitySuite\Model\Mismatch;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

/**
 * A single mismatch, as serialized by Mismatch::to_json()
 * (see pact-reference/rust/pact_matching/src/lib.rs).
 */
final class Mismatch
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getType(): string
    {
        return TypeCaster::toString($this->data['type'] ?? '');
    }

    public function getMismatch(): string
    {
        return TypeCaster::toString($this->data['mismatch'] ?? '');
    }

    public function getExpected(): string
    {
        return TypeCaster::toString($this->data['expected'] ?? '');
    }

    public function getActual(): string
    {
        return TypeCaster::toString($this->data['actual'] ?? '');
    }

    public function getPath(): ?string
    {
        return isset($this->data['path']) ? TypeCaster::toString($this->data['path']) : null;
    }

    public function getKey(): ?string
    {
        return isset($this->data['key']) ? TypeCaster::toString($this->data['key']) : null;
    }

    public function getParameter(): ?string
    {
        return isset($this->data['parameter']) ? TypeCaster::toString($this->data['parameter']) : null;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
