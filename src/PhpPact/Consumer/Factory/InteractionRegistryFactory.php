<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\Part\RequestDriver;
use PhpPact\Consumer\Driver\Interaction\Part\ResponseDriver;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Service\InteractionRegistry;
use PhpPact\Consumer\Service\InteractionRegistryInterface;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;

class InteractionRegistryFactory
{
    public static function create(MockServerConfigInterface $config): InteractionRegistryInterface
    {
        $client = new Client();
        $pactDriver = new PactDriver($client, $config);
        $interactionDriver = new InteractionDriver($client, $pactDriver);
        $requestDriver = new RequestDriver($client, $interactionDriver);
        $responseDriver = new ResponseDriver($client, $interactionDriver);
        $mockServer = new MockServer($client, $pactDriver, $config);

        return new InteractionRegistry($interactionDriver, $requestDriver, $responseDriver, $mockServer);
    }
}
