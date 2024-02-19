<?php

namespace PhpPact\Plugins\Csv\Factory;

use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\FFI\Client;
use PhpPact\Plugin\Driver\Body\PluginBodyDriver;
use PhpPact\Plugins\Csv\Driver\Body\CsvBodyDriver;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\Plugins\Csv\Driver\Pact\CsvPactDriver;

class CsvInteractionDriverFactory implements InteractionDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): InteractionDriverInterface
    {
        $client = new Client();
        $pactDriver = new CsvPactDriver($client, $config);
        $mockServer = new MockServer($client, $config);
        $csvBodyDriver = new CsvBodyDriver(new PluginBodyDriver($client));
        $responseDriver = new ResponseDriver($client, $csvBodyDriver);

        return new InteractionDriver($client, $mockServer, $pactDriver, null, $responseDriver);
    }
}
