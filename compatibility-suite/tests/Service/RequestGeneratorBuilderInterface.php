<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;

interface RequestGeneratorBuilderInterface
{
    public function build(ConsumerRequest $request, string $value): void;
}
