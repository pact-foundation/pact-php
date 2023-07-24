<?php

namespace PhpPact\Consumer\Registry\Interaction\Part;

interface RequestRegistryInterface extends PartRegistryInterface
{
    /**
     * @param array<string, string[]> $queryParams
     */
    public function withQueryParameters(array $queryParams): self;

    public function withRequest(string $method, string $path): self;
}
