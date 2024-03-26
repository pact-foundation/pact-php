<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ProviderResponse;

interface ResponseMatchingRuleBuilderInterface
{
    public function build(ProviderResponse $response, string $file): void;
}
