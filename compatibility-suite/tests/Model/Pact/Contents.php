<?php

namespace PhpPactTest\CompatibilitySuite\Model\Pact;

use PhpPactTest\CompatibilitySuite\Util\TypeCaster;

final class Contents
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    /**
     * @return mixed the raw (decoded JSON) content
     */
    public function getContent(): mixed
    {
        return $this->data['content'] ?? null;
    }

    public function getContentType(): string
    {
        return TypeCaster::toString($this->data['contentType'] ?? '');
    }
}
