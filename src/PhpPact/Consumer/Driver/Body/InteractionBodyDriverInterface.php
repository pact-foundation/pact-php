<?php

namespace PhpPact\Consumer\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;

interface InteractionBodyDriverInterface
{
    public function registerBody(Interaction $interaction, InteractionPart $part): void;
}
