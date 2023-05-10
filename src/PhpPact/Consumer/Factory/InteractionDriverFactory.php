<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;

class InteractionDriverFactory
{
    public static function create(MockServerConfigInterface $config): InteractionDriverInterface
    {
        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $pactDriver = new PactDriver($client, $config, $pactRegistry);
        $mockServer = new MockServer($client, $pactRegistry, $config);
        $interactionRegistry = new InteractionRegistry($client, $pactRegistry);

        return new InteractionDriver($pactDriver, $interactionRegistry, $mockServer);
    }
}
