<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

interface PartDriverInterface
{
    public function withBody(?string $contentType = null, ?string $body = null): void;

    /**
     * @param array<string, string[]> $headers
     */
    public function withHeaders(array $headers): void;
}
