<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\Runner\ProcessRunner;

/**
 * Ruby Standalone Stub Server Wrapper
 * Class StubServer.
 */
class StubServer
{
    /** @var StubServerConfigInterface */
    private $config;

    /** @var InstallManager */
    private $installManager;

    /** @var ProcessRunner */
    private $processRunner;

    public function __construct(StubServerConfigInterface $config)
    {
        $this->config         = $config;
        $this->installManager = new InstallManager();
    }

    /**
     * Start the Stub Server. Verify that it is running.
     *
     * @param int $wait seconds to delay for the server to come up
     *
     * @throws Exception
     *
     * @return int process ID of the started Stub Server
     */
    public function start($wait = 1): int
    {
        $scripts = $this->installManager->install();

        $this->processRunner = new ProcessRunner($scripts->getStubService(), $this->getArguments());

        $processId =  $this->processRunner->run();
        \sleep($wait); // wait for server to start

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
