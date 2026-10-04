<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

final class ProviderState
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function getName(): string
    {
        return TypeCaster::toString($this->data['name'] ?? '');
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getParams(): array
    {
        return (array) ($this->data['params'] ?? []);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
