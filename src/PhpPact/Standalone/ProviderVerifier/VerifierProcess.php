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
     * VerifierProcess constructor.
     *
     * @param null|InstallManager $installManager
     */
    public function __construct(InstallManager $installManager)
    {
        $this->installManager = $installManager;
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

        $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
        $logHandler->setFormatter(new ConsoleFormatter);
        $logger = new Logger('console');
        $logger->pushHandler($logHandler);

        $logger->addInfo("Verifying PACT with script:\n{$processRunner->getCommand()}\n\n");
        $processRunner->runBlocking();

        $logger->addInfo('out > ' . $processRunner->getOutput());
        $logger->addError('err > ' . $processRunner->getStderr());
    }
}
