<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Interaction;

interface InteractionDriverInterface
{
    public function registerInteraction(Interaction $interaction): bool;

    public function verifyInteractions(): bool;
}
