<?php

namespace PhpPact\Plugins\Protobuf\Driver\Body;

use PhpPact\Consumer\Driver\Body\MessageBodyDriverInterface;
use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Model\Message;
use PhpPact\Plugin\Driver\Body\PluginBodyDriverInterface;

class ProtobufMessageBodyDriver implements MessageBodyDriverInterface
{
    public function __construct(private PluginBodyDriverInterface $pluginBodyDriver)
    {
    }

    public function registerBody(Message $message): void
    {
        $this->pluginBodyDriver->registerBody($message, InteractionPart::REQUEST);
    }
}
