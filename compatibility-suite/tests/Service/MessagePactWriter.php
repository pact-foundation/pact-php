<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Factory\MessageDriverFactory;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

class MessagePactWriter implements MessagePactWriterInterface
{
    public function __construct(
        private ParserInterface $parser,
        private string $specificationVersion,
    ) {
    }

    public function write(string $name, string $body, PactPath $pactPath, string $mode = PactConfigInterface::MODE_OVERWRITE): void
    {
        $config = new MockServerConfig();
        $config
            ->setConsumer($pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($this->specificationVersion)
            ->setPactFileWriteMode($mode);
        $driver = (new MessageDriverFactory())->create($config);

        $message = new Message();
        $message->setDescription($name);
        $message->setContents($this->parser->parseBody($body));
        $driver->registerMessage($message);
        $driver->writePactAndCleanUp();
    }
}
