<?php

namespace PhpPact\Plugins\Csv\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\Plugins\Csv\Driver\Pact\CsvPactDriver;
use PhpPact\Plugins\Csv\Registry\Interaction\CsvInteractionRegistry;

class CsvInteractionDriverFactory implements InteractionDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): InteractionDriverInterface
    {
        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $pactDriver = new CsvPactDriver($client, $config, $pactRegistry);
        $mockServer = new MockServer($client, $pactRegistry, $config);
        $interactionRegistry = new CsvInteractionRegistry($client, $pactRegistry);

        return new InteractionDriver($pactDriver, $interactionRegistry, $mockServer);
    }
}
