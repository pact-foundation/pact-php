<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ProviderResponse;

interface ResponseBuilderInterface
{
    public function build(ProviderResponse $response, array $data): void;
}
