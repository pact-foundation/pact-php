<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

interface RequestDriverInterface extends PartDriverInterface
{
    /**
     * @param array<string, string[]> $queryParams
     */
    public function withQueryParameters(array $queryParams): void;

    public function withRequest(string $method, string $path): void;
}
