<?php

namespace PhpPact\Plugin\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Consumer\Model\Message;

interface PluginBodyDriverInterface
{
    public function registerBody(Interaction|Message $interaction, InteractionPart $part): void;
}
