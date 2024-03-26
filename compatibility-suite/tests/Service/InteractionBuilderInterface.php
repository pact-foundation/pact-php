<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Interaction;

interface InteractionBuilderInterface
{
    public function build(array $data): Interaction;
}
