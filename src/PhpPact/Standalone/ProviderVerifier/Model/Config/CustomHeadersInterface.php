<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface CustomHeadersInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function setHeaders(array $headers): self;

    public function addHeader(string $name, string $value): self;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
}
