<?php

namespace PhpPact\Plugins\Csv\Registry\Interaction;

use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\Consumer\Registry\Interaction\Part\RequestRegistryInterface;
use PhpPact\Consumer\Registry\Interaction\Part\ResponseRegistry;
use PhpPact\Consumer\Registry\Interaction\Part\ResponseRegistryInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Plugins\Csv\Registry\Interaction\Body\CsvResponseBodyRegistry;

class CsvInteractionRegistry extends InteractionRegistry
{
    public function __construct(
        ClientInterface $client,
        PactDriverInterface $pactDriver,
        ?RequestRegistryInterface $requestRegistry = null,
        ?ResponseRegistryInterface $responseRegistry = null,
    ) {
        $responseRegistry = $responseRegistry ?? new ResponseRegistry($client, $this, new CsvResponseBodyRegistry($client, $this));
        parent::__construct($client, $pactDriver, $requestRegistry, $responseRegistry);
    }
}
