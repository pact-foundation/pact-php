<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Registry\Interaction\InteractionRegistry;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;

class PactWriter implements PactWriterInterface
{
    private string $pactPath;

    public function __construct(
        private InteractionsStorageInterface $storage,
        private string $specificationVersion,
    ) {
    }

    public function write(int $id, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void
    {
        $pactDir = Path::PACTS_PATH;
        $this->pactPath = "$pactDir/$consumer-$provider.json";
        $config = new MockServerConfig();
        $config
            ->setConsumer($consumer)
            ->setProvider($provider)
            ->setPactDir($pactDir)
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

    public function getPactPath(): string
    {
        return $this->pactPath;
    }
}
