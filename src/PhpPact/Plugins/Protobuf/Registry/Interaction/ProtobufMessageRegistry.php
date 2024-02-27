<?php

namespace PhpPact\Plugins\Protobuf\Registry\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Registry\Interaction\Body\BodyRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\MessageRegistry;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugins\Protobuf\Registry\Interaction\Body\ProtobufMessageContentsRegistry;

class ProtobufMessageRegistry extends MessageRegistry
{
    public function __construct(
        ClientInterface $client,
        PactDriverInterface $pactDriver,
        ?BodyRegistryInterface $messageContentsRegistry = null
    ) {
        parent::__construct($client, $pactDriver, $messageContentsRegistry ?? new ProtobufMessageContentsRegistry($client, $this));
    }
}
