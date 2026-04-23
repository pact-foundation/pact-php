<?php

namespace PhpPactTest\CompatibilitySuite\Plugin;

use PhpPact\FFI\Client;
use PhpPact\SyncMessage\Driver\Body\SyncMessageBodyDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriver;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactoryInterface;

class SyncMessageTestDriverFactory implements SyncMessageDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): SyncMessageDriverInterface
    {
        $client = new Client();
        $pactDriver = new SyncMessageTestPactDriver($client, $config);
        $mockServer = new TcpMockServer($client, $pactDriver, $config);
        $syncMessageBodyDriver = new SyncMessageBodyDriver($client);

        return new SyncMessageDriver($mockServer, $client, $pactDriver, $syncMessageBodyDriver);
    }
}
