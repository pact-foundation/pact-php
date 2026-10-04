<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ProviderResponse;

interface ResponseBuilderInterface
{
    /**
     * @param array<array-key, string|int> $data
     */
    public function build(ProviderResponse $response, array $data): void;
}
