<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\Interaction;
use PhpPact\Standalone\MockService\Model\VerifyResult;

interface InteractionDriverInterface extends DriverInterface
{
    public function registerInteraction(Interaction $interaction, bool $startMockServer = true): bool;

    public function verifyInteractions(): VerifyResult;
}
