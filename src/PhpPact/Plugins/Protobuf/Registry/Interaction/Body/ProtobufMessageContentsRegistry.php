<?php

namespace PhpPact\Plugins\Protobuf\Registry\Interaction\Body;

use PhpPact\Consumer\Registry\Interaction\MessageRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Part\RequestPartTrait;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugin\Registry\Interaction\Body\AbstractPluginBodyRegistry;

class ProtobufMessageContentsRegistry extends AbstractPluginBodyRegistry
{
    use RequestPartTrait;

    public function __construct(
        protected ClientInterface $client,
        private MessageRegistryInterface $messageRegistry
    ) {
        parent::__construct($client);
    }

    protected function getInteractionId(): int
    {
        return $this->messageRegistry->getId();
    }
}
