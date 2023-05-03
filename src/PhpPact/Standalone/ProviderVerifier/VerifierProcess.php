<?php

namespace PhpPact\Standalone\ProviderVerifier;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class VerifierProcess
{
    private ?LoggerInterface $logger = null;

    private ProcessRunnerFactory $processRunnerFactory;

    public function __construct(ProcessRunnerFactory $processRunnerFactory = null)
    {
        $this->processRunnerFactory = $processRunnerFactory ?: new ProcessRunnerFactory();
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param array<int, string> $arguments
     * @throws \Exception
     */
    public function run(array $arguments, ?int $processTimeout = null, ?int $processIdleTimeout = null): void
    {
        $logger        = $this->getLogger();
        $processRunner = $this->processRunnerFactory->createRunner(
            $arguments,
            $logger
        );

        $logger->info("Verifying PACT with script:\n{$processRunner->getCommand()}\n\n");

        $processRunner->runBlocking();
    }

    private function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter(null, null, true));
            $this->logger = new Logger('console');
            $this->logger->pushHandler($logHandler);
        }

        return $this->logger;
    }
}
