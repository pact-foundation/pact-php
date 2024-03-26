<?php

namespace PhpPact\SyncMessage\Factory;

use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\FFI\Client;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class SyncMessageDriverFactory implements SyncMessageDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): SyncMessageDriverInterface
    {
        $client = new Client();
        $pactDriver = new PactDriver($client, $config);
        $mockServer = new MockServer($client, $pactDriver, $config);

        return new SyncMessageDriver($mockServer, $client, $pactDriver);
    }
}
