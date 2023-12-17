<?php

namespace PhpPact\SyncMessage\Factory;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\FFI\Client;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\SyncMessage\Registry\Interaction\SyncMessageRegistry;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class SyncMessageDriverFactory implements SyncMessageDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): SyncMessageDriverInterface
    {
        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $pactDriver = new PactDriver($client, $config, $pactRegistry);
        $messageRegistry = new SyncMessageRegistry($client, $pactRegistry);
        $mockServer = new MockServer($client, $pactRegistry, $config);

        return new SyncMessageDriver($pactDriver, $messageRegistry, $mockServer);
    }
}
