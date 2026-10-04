<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;

interface RequestBuilderInterface
{
    /**
     * @param array<array-key, string|int> $data
     */
    public function build(ConsumerRequest $request, array $data): void;
}
