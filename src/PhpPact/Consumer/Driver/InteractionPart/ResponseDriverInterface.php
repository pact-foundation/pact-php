<?php

namespace PhpPact\Consumer\Driver\InteractionPart;

use PhpPact\Consumer\Model\Interaction;

interface ResponseDriverInterface
{
    public function registerResponse(Interaction $interaction): void;
}
