<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\Runner\ProcessRunner;

/**
 * Ruby Standalone Stub Server Wrapper
 * Class StubServer.
 */
class StubServer
{
    /** @var StubServerConfigInterface */
    private $config;

    /** @var ProcessRunner */
    private $processRunner;

    public function __construct(StubServerConfigInterface $config)
    {
        $this->config         = $config;
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
        $this->processRunner = new ProcessRunner(Scripts::getStubService(), $this->getArguments());

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
