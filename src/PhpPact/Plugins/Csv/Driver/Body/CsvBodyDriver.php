<?php

namespace PhpPact\Plugins\Csv\Driver\Body;

use PhpPact\Consumer\Driver\Body\InteractionBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Interaction;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;

class CsvBodyDriver implements InteractionBodyDriverInterface
{
    public function __construct(private PluginBodyDriverInterface $pluginBodyDriver)
    {
    }

    public function registerBody(Interaction $interaction, InteractionPart $part): void
    {
        $this->pluginBodyDriver->registerBody($interaction, $part);
    }
}
