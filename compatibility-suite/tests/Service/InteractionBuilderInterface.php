<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Interaction;

interface InteractionBuilderInterface
{
    /**
     * @param array<array-key, string|int> $data
     */
    public function build(array $data): Interaction;
}
