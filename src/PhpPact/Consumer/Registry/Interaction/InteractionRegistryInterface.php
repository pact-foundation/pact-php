<?php

namespace PhpPact\Consumer\Registry\Interaction;

use PhpPact\Consumer\Model\Interaction;

interface InteractionRegistryInterface extends RegistryInterface
{
    public function registerInteraction(Interaction $interaction): bool;
}
