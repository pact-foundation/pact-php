<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Factory\MessageDriverFactory;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;

class MessagePactWriter implements MessagePactWriterInterface
{
    private string $pactPath;

    public function __construct(
        private ParserInterface $parser,
        private string $specificationVersion,
    ) {
    }

    public function write(string $name, string $body, string $consumer = 'c', string $provider = 'p', string $mode = PactConfigInterface::MODE_OVERWRITE): void
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
        $driver = (new MessageDriverFactory())->create($config);

        $message = new Message();
        $message->setDescription($name);
        $message->setContents($this->parser->parseBody($body));
        $driver->registerMessage($message);
        $driver->writePactAndCleanUp();
    }

    public function getPactPath(): string
    {
        return $this->pactPath;
    }
}
