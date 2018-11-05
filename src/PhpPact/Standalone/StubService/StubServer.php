<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\Runner\ProcessRunner;
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

    /** @var Filesystem */
    private $fileSystem;

    /** @var ConsoleOutput */
    private $console;

    /** @var ProcessRunner */
    private $processRunner;

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
     * @throws Exception
     *
     * @return int process ID of the started Stub Server
     */
    public function start(): int
    {
        $scripts = $this->installManager->install();

        $this->processRunner = new ProcessRunner($scripts->getStubService(), $this->getArguments());

        $processId =  $this->processRunner->run($blocking = false);
        \sleep(1); // wait for server to start

        return $processId;
    }

    /**
     * Stop the Stub Server process.
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        return $this->processRunner->stop();
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
