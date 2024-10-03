<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\SyncMessage\Factory\SyncMessageDriverFactory;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

class SyncMessagePactWriter implements SyncMessagePactWriterInterface
{
    public function __construct(
        private string $specificationVersion,
    ) {
    }

    public function write(Message $message, PactPath $pactPath, WriteMode $mode = WriteMode::OVERWRITE): void
    {
        $config = new MockServerConfig();
        $config
            ->setConsumer($pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($this->specificationVersion)
            ->setPactFileWriteMode($mode);
        $driver = (new SyncMessageDriverFactory())->create($config);

        $driver->registerMessage($message);
        $driver->writePactAndCleanUp();
    }
}
