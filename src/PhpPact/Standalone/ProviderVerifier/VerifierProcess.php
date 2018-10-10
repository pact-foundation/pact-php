<?php

namespace PhpPact\Standalone\ProviderVerifier;

use PhpPact\Standalone\Installer\InstallManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Process\Process;

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

        $arguments = \array_merge([$scripts->getProviderVerifier()], $arguments);

        $process = new Process($arguments, null, null, null, $processTimeout);
        $process->setIdleTimeout($processIdleTimeout);

        $cmd = $process->getCommandLine();

        // handle deps=low requirements
        if (\is_array($cmd)) {
            $cmd = \implode(' ', $cmd);
        }

        $this->output->write("Verifying PACT with script:\n{$cmd}\n\n");

        $process->mustRun(
            function ($type, $buffer) {
                $this->output->write("{$type} > {$buffer}");
            }
        );
    }
}
