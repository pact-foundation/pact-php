<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\Runner\ProcessRunner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Ruby Standalone Stub Server Wrapper
 * Class StubServer.
 */
class StubServer
{
    /** @var StubServerConfig */
    private $config;

    /** @var InstallManager */
    private $installManager;

    /** @var Process */
    private $process;

    /** @var Filesystem */
    private $fileSystem;

    /** @var ConsoleOutput */
    private $console;

    public function __construct(StubServerConfigInterface $config)
    {
        $this->config         = $config;
        $this->installManager = new InstallManager();
        $this->fileSystem     = new Filesystem();
        $this->console        = new ConsoleOutput();
    }

    /**
     * Start the Stub Server. Verify that it is running.
     *
     * @throws ProcessFailedException
     * @throws Exception
     *
     * @return int process ID of the started Stub Server
     */
    public function start(): int
    {
        $scripts = $this->installManager->install();

        $this->process = ProcessRunner::run($scripts->getStubService(), $this->getArguments());

        $this->process
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $this->console->writeln("Starting the stub service with command {$this->process->getCommandLine()}");

        $this->process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->console->write($buffer);
            } else {
                $this->console->write($buffer);
            }
        });
        \sleep(1);

        if ($this->process->isStarted() !== true || $this->process->isRunning() !== true) {
            throw new ProcessFailedException($this->process);
        }

        return $this->process->getPid();
    }

    /**
     * Stop the Stub Server process.
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        $exitCode = $this->process->stop();
        $this->console->writeln("Process exited with code {$exitCode}.");

        return true;
    }

    /**
     * Wrapper to add a custom installer.
     *
     * @param InstallerInterface $installer
     *
     * @return self
     */
    public function registerInstaller(InstallerInterface $installer): self
    {
        $this->installManager->registerInstaller($installer);

        return $this;
    }

    /**
     * Build an array of command arguments.
     *
     * @return array
     */
    private function getArguments(): array
    {
        $results = [];

        $results[] = $this->config->getPactLocation();
        $results[] = "--host={$this->config->getHost()}";
        $results[] = "--port={$this->config->getPort()}";

        if ($this->config->getLog() !== null) {
            $results[] = "--log={$this->config->getLog()}";
        }

        return $results;
    }
}
