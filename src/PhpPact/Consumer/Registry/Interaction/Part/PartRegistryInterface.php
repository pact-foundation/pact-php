<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

interface PartRegistryInterface
{
    public function withBody(?string $contentType = null, ?string $body = null): self;

    /**
     * @param array<string, string[]> $headers
     */
    public function withHeaders(array $headers): self;
}
