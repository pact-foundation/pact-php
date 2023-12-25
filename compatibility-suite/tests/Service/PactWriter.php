<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

class PactWriter implements PactWriterInterface
{
    public function __construct(
        private InteractionsStorageInterface $storage,
        private string $specificationVersion,
    ) {
    }

    public function write(int $id, PactPath $pactPath, string $mode = PactConfigInterface::MODE_OVERWRITE): void
    {
        $config = new MockServerConfig();
        $config
            ->setConsumer($pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($this->specificationVersion)
            ->setPactFileWriteMode($mode);
        $client = new Client();
        $pactRegistry = new PactRegistry($client);
        $pactDriver = new PactDriver($client, $config, $pactRegistry);
        $interactionRegistry = new InteractionRegistry($client, $pactRegistry);

        $interaction = $this->storage->get(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $id);
        $pactDriver->setUp();
        $interactionRegistry->registerInteraction($interaction);
        $pactDriver->writePact();
        $pactDriver->cleanUp();
    }
}
