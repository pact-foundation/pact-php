<?php

namespace PhpPact\Plugins\Csv\Factory;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PhpPact\Consumer\Driver\Interaction\InteractionDriver;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Driver\InteractionPart\RequestDriver;
use PhpPact\Consumer\Driver\InteractionPart\ResponseDriver;
use PhpPact\Consumer\Factory\InteractionDriverFactoryInterface;
use PhpPact\FFI\Client;
use PhpPact\Plugin\Driver\Body\PluginBodyDriver;
use PhpPact\Plugins\Csv\Driver\Body\CsvBodyDriver;
use PhpPact\Plugins\Csv\Exception\MissingPluginPartsException;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\Plugins\Csv\Driver\Pact\CsvPactDriver;

class CsvInteractionDriverFactory implements InteractionDriverFactoryInterface
{
    /**
     * @var InteractionPart[]
     */
    private array $pluginParts;

    public function __construct(InteractionPart ...$pluginParts)
    {
        if (empty($pluginParts)) {
            throw new MissingPluginPartsException('At least 1 interaction part must be csv');
        }
        $this->pluginParts = $pluginParts;
    }

    public function create(MockServerConfigInterface $config): InteractionDriverInterface
    {
        $client = new Client();
        $pactDriver = new CsvPactDriver($client, $config);
        $mockServer = new MockServer($client, $pactDriver, $config);
        $csvBodyDriver = new CsvBodyDriver(new PluginBodyDriver($client));
        $requestDriver = in_array(InteractionPart::REQUEST, $this->pluginParts) ? new RequestDriver($client, $csvBodyDriver) : null;
        $responseDriver = in_array(InteractionPart::RESPONSE, $this->pluginParts) ? new ResponseDriver($client, $csvBodyDriver) : null;

        return new InteractionDriver($client, $mockServer, $pactDriver, $requestDriver, $responseDriver);
    }
}
