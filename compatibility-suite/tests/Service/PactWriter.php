<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Factory\InteractionDriverFactory;
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

    public function write(int $id, PactPath $pactPath, WriteMode $mode = WriteMode::OVERWRITE): void
    {
        $config = new MockServerConfig();
        $config
            ->setConsumer($pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($this->specificationVersion)
            ->setPactFileWriteMode($mode);
        $driver = (new InteractionDriverFactory())->create($config);

        $interaction = $this->storage->get(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $id);
        $driver->registerInteraction($interaction);
        $driver->writePactAndCleanUp();
    }
}
