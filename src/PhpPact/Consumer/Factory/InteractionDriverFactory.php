<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;

class InteractionDriverFactory implements InteractionDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): InteractionDriverInterface
    {
        $client = new Client();
        $pactDriver = new PactDriver($client, $config);
        $mockServer = new MockServer($client, $pactDriver, $config);
        $interactionRegistry = new InteractionRegistry($client, $pactDriver);

        return new InteractionDriver($interactionRegistry, $mockServer);
    }
}
