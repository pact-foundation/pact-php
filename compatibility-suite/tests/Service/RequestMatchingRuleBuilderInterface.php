<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;

interface RequestMatchingRuleBuilderInterface
{
    public function build(ConsumerRequest $request, string $file): void;
}
