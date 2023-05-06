<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Service\InteractionRegistry;
use PhpPact\Consumer\Service\InteractionRegistryInterface;
use PhpPact\FFI\Proxy;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;

class InteractionRegistryFactory
{
    public static function create(MockServerConfigInterface $config): InteractionRegistryInterface
    {
        $proxy = new Proxy();
        $pactDriver = new PactDriver($proxy, $config);
        $interactionDriver = new InteractionDriver($proxy, $pactDriver);
        $mockServer = new MockServer($proxy, $pactDriver, $config);

        return new InteractionRegistry($interactionDriver, $mockServer);
    }
}
