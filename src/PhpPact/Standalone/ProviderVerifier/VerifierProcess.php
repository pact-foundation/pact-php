<?php

namespace PhpPact\Standalone\ProviderVerifier;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use PhpPact\Standalone\Installer\InstallManager;
use Psr\Log\LoggerInterface;

class VerifierProcess
{
    /**
     * @var InstallManager
     */
    private $installManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProcessRunnerFactory
     */
    private $processRunnerFactory;

    /**
     * VerifierProcess constructor.
     *
     * @param InstallManager       $installManager
     * @param ProcessRunnerFactory $processRunnerFactory
     */
    public function __construct(InstallManager $installManager, ProcessRunnerFactory $processRunnerFactory = null)
    {
        $this->installManager       = $installManager;
        $this->processRunnerFactory = $processRunnerFactory?: new ProcessRunnerFactory();
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return VerifierProcess
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Execute the Pact Verifier Service.
     *
     * @param array $arguments
     * @param int   $processTimeout
     * @param int   $processIdleTimeout
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function run(array $arguments, $processTimeout, $processIdleTimeout)
    {
        $scripts = $this->installManager->install();

        $logger        = $this->getLogger();
        $processRunner = $this->processRunnerFactory->createRunner(
            $scripts->getProviderVerifier(),
            $arguments,
            $logger
        );

        $logger->info("Verifying PACT with script:\n{$processRunner->getCommand()}\n\n");

        try {
            $processRunner->runBlocking();

            $logger->info('out > ' . $processRunner->getOutput());
            $logger->error('err > ' . $processRunner->getStderr());
        } catch (\Exception $e) {
            $logger->info('out > ' . $processRunner->getOutput());
            $logger->error('err > ' . $processRunner->getStderr());

            throw $e;
        }
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
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
