<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

interface PartDriverInterface
{
    public function withBody(?string $contentType = null, ?string $body = null): self;

    /**
     * @param array<string, string[]> $headers
     */
    public function withHeaders(array $headers): self;
}
