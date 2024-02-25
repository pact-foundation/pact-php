<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Model\Interaction;

interface RequestDriverInterface
{
    public function registerRequest(Interaction $interaction): void;
}
