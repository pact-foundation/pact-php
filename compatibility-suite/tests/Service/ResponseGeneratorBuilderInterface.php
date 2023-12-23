<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ProviderResponse;

interface ResponseGeneratorBuilderInterface
{
    public function build(ProviderResponse $response, string $value): void;
}
