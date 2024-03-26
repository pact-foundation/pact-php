<?php

namespace PhpPact\Plugins\Protobuf\Factory;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Interaction\MessageDriver;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Factory\MessageDriverFactoryInterface;
use PhpPact\FFI\Client;
use PhpPact\Plugin\Driver\Body\PluginBodyDriver;
use PhpPact\Plugins\Protobuf\Driver\Body\ProtobufMessageBodyDriver;
use PhpPact\Plugins\Protobuf\Driver\Pact\ProtobufPactDriver;

class ProtobufMessageDriverFactory implements MessageDriverFactoryInterface
{
    public function create(PactConfigInterface $config): MessageDriverInterface
    {
        $client = new Client();
        $pactDriver = new ProtobufPactDriver($client, $config);
        $messageBodyDriver = new ProtobufMessageBodyDriver(new PluginBodyDriver($client));

        return new MessageDriver($client, $pactDriver, $messageBodyDriver);
    }
}
