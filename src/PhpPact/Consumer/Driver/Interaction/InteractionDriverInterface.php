<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Interaction;

interface InteractionDriverInterface extends DriverInterface
{
    public function registerInteraction(Interaction $interaction): void;
}
