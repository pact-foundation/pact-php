<?php

namespace PhpPact\Standalone\ProviderVerifier;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Runner\ProcessRunner;

class VerifierProcess
{
    /**
     * @var InstallManager
     */
    private $installManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * VerifierProcess constructor.
     *
     * @param null|InstallManager $installManager
     */
    public function __construct(InstallManager $installManager)
    {
        $this->installManager = $installManager;
    }

    /**
     * @param Logger $logger
     *
     * @return VerifierProcess
     */
    public function setLogger(Logger $logger): self
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

        $processRunner = new ProcessRunner($scripts->getProviderVerifier(), $arguments);

        $logger = $this->getLogger();

        $logger->addInfo("Verifying PACT with script:\n{$processRunner->getCommand()}\n\n");

        try {
            $processRunner->runBlocking();

            $logger->addInfo('out > ' . $processRunner->getOutput());
            $logger->addError('err > ' . $processRunner->getStderr());
        } catch (\Exception $e) {
            $logger->addInfo('out > ' . $processRunner->getOutput());
            $logger->addError('err > ' . $processRunner->getStderr());

            throw $e;
        }
    }

    /**
     * @return Logger
     */
    private function getLogger()
    {
        if (null === $this->logger) {
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter);
            $this->logger = new Logger('console');
            $this->logger->pushHandler($logHandler);
        }

        return $this->logger;
    }
}
