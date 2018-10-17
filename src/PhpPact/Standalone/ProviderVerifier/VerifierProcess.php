<?php

namespace PhpPact\Standalone\ProviderVerifier;

use Amp\Process\Process;
use Amp\Process\ProcessException;
use PhpPact\Standalone\Installer\InstallManager;
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

        $process = new Process($scripts->getProviderVerifier() . ' ' . \implode(' ', $arguments));
        $process->start();

        $process->getStdout()->read()->onResolve(function ($error, $value) {
            $this->output->writeln("out > {$value}");
        });
        $process->getStderr()->read()->onResolve(function ($error, $value) {
            $this->output->writeln("out > {$value}");
        });

        \Amp\Loop::run(function () use ($process) {
            yield $process->getPid();

            if (!$process->isRunning()) {
                throw new ProcessException('Failed to start mock server');
            }

            $this->output->write("Verifying PACT with script:\n{$process->getCommand()}\n\n");

            yield $process->join();

            \Amp\Loop::delay($msDelay = 100, 'Amp\\Loop::stop');
        });
    }
}
