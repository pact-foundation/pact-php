<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Runner\ProcessRunner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

class VerifierProcess
{
    /**
     * @var InstallManager
     */
    private $installManager;

    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * VerifierProcess constructor.
     *
     * @param null|InstallManager         $installManager
     * @param null|ConsoleOutputInterface $output
     */
    public function __construct(InstallManager $installManager, ConsoleOutputInterface $output)
    {
        $this->installManager = $installManager;
        $this->output         = $output;
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

        $this->output->write("Verifying PACT with script:\n{$processRunner->getCommand()}\n\n");
        $processRunner->runBlocking();

        $this->output->writeln('out > ' . $processRunner->getOutput());
        $this->output->writeln('err > ' . $processRunner->getStderr());
    }
}
