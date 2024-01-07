<?php

namespace PhpPact\Plugins\Protobuf\Factory;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Interaction\MessageDriver;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Plugins\Protobuf\Driver\Pact\ProtobufPactDriver;
use PhpPact\Plugins\Protobuf\Registry\Interaction\ProtobufMessageRegistry;

class ProtobufMessageDriverFactory implements MessageDriverFactoryInterface
{
    public function create(PactConfigInterface $config): MessageDriverInterface
    {
        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $pactDriver = new ProtobufPactDriver($client, $config, $pactRegistry);
        $messageRegistry = new ProtobufMessageRegistry($client, $pactRegistry);

        return new MessageDriver($client, $pactDriver, $messageRegistry);
    }
}
