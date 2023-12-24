<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriver;
use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Registry\Pact\PactRegistry;
use PhpPact\FFI\Client;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\SyncMessage\Registry\Interaction\SyncMessageRegistry;
use PhpPactTest\CompatibilitySuite\Constant\Path;

class SyncMessagePactWriter implements SyncMessagePactWriterInterface
{
    private string $pactPath;

    public function __construct(
        private string $specificationVersion,
    ) {
    }

    public function write(Message $message, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void
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
        $messageRegistry = new SyncMessageRegistry($client, $pactRegistry);

        $pactDriver->setUp();
        $messageRegistry->registerMessage($message);
        $pactDriver->writePact();
        $pactDriver->cleanUp();
    }

    public function getPactPath(): string
    {
        return $this->pactPath;
    }
}
