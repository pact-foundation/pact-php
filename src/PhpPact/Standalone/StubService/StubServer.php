<?php

namespace PhpPact\Standalone\StubService;

use Amp\Process\Process;
use Amp\Process\ProcessException;
use Exception;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

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
     * @throws ProcessException
     * @throws Exception
     *
     * @return int process ID of the started Stub Server
     */
    public function start(): int
    {
        $scripts = $this->installManager->install();

        $processId = null;
        \Amp\Loop::run(function () use ($scripts, &$processId) {
            $this->process = new Process($scripts->getStubService() . ' ' . \implode(' ', $this->getArguments()));

            $this->console->writeln("Starting the mock service with command {$this->process->getCommand()}");
            $this->process->start();

            $processId = yield $this->process->getPid();

            $stream = $this->process->getStdout();
            $this->console->write(yield $stream->read());

            if (!$this->process->isRunning()) {
                throw new ProcessException('Failed to start stub server');
            }

            \Amp\Loop::delay($msDelay = 100, 'Amp\\Loop::stop');
        });

        return $processId;
    }

    /**
     * Stop the Stub Server process.
     *
     * @throws ProcessException
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        $this->process->kill();

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
