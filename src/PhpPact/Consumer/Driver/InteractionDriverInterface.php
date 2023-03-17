<?php

namespace PhpPact\Consumer\Driver;

use PhpPact\Consumer\Model\Interaction;

interface InteractionDriverInterface extends DriverInterface
{
    public function verifyInteractions(): bool;

    public function registerInteraction(Interaction $interaction): bool;
}
