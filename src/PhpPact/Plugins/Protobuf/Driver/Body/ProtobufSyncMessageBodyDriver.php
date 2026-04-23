<?php

namespace PhpPact\Plugins\Protobuf\Driver\Body;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;
use PhpPact\SyncMessage\Driver\Body\SyncMessageBodyDriverInterface;
use PhpPact\SyncMessage\Model\SyncMessage;

class ProtobufSyncMessageBodyDriver implements SyncMessageBodyDriverInterface
{
    public function __construct(private readonly PluginBodyDriverInterface $pluginBodyDriver)
    {
    }

    public function registerBody(SyncMessage $message): void
    {
        $this->pluginBodyDriver->registerBody($message, InteractionPart::REQUEST);
    }
}
