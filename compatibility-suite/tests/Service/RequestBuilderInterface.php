<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;

interface RequestBuilderInterface
{
    public function build(ConsumerRequest $request, array $data): void;
}
