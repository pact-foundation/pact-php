<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Model\Interaction;

interface InteractionRegistryInterface
{
    public function verifyInteractions(): bool;

    public function registerInteraction(Interaction $interaction): bool;
}
