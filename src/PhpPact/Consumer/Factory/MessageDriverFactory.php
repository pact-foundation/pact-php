<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Consumer\Driver\Interaction\MessageDriver;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Registry\Interaction\MessageRegistry;
use PhpPact\FFI\Client;

class MessageDriverFactory implements MessageDriverFactoryInterface
{
    public function create(PactConfigInterface $config): MessageDriverInterface
    {
        $client = new Client();
        $pactDriver = new PactDriver($client, $config);
        $messageRegistry = new MessageRegistry($client, $pactDriver);

        return new MessageDriver($client, $pactDriver, $messageRegistry);
    }
}
