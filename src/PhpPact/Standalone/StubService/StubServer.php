<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\Model\Scripts;
use Symfony\Component\Process\Process;

/**
 * Ruby Standalone Stub Server Wrapper
 */
class StubServer
{
    private StubServerConfigInterface $config;

    private Process $process;

    public function __construct(StubServerConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Start the Stub Server. Verify that it is running.
     *
     * @throws Exception
     *
     * @return int|null process ID of the started Stub Server if running, null otherwise
     */
    public function start(): ?int
    {
        $this->process = new Process([Scripts::getStubService(), ...$this->getArguments()]);

        $this->process->start(function (string $type, string $buffer) {
            echo $buffer;
        });
        $this->process->waitUntil(function (string $type, string $output) {
            return false !== \strpos($output, 'Server started on port');
        });

        return $this->process->getPid();
    }

    /**
     * Stop the Stub Server process.
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        $this->process->stop();

        return true;
    }

    /**
     * Build an array of command arguments.
     *
     * @return array<int, string>
     */
    private function getArguments(): array
    {
        $results = [];

        if ($this->config->getBrokerUrl() !== null) {
            $results[] = "--broker-url={$this->config->getBrokerUrl()}";
        }

        foreach ($this->config->getDirs() as $dir) {
            $results[] = "--dir={$dir}";
        }

        if ($this->config->getExtension() !== null) {
            $results[] = "--extension={$this->config->getExtension()}";
        }

        foreach ($this->config->getFiles() as $file) {
            $results[] = "--file={$file}";
        }

        if ($this->config->getLogLevel() !== null) {
            $results[] = "--loglevel={$this->config->getLogLevel()}";
        }

        if ($this->config->getPort() !== null) {
            $results[] = "--port={$this->config->getPort()}";
        }

        if ($this->config->getProviderState() !== null) {
            $results[] = "--provider-state={$this->config->getProviderState()}";
        }

        if ($this->config->getProviderStateHeaderName() !== null) {
            $results[] = "--provider-state-header-name={$this->config->getProviderStateHeaderName()}";
        }

        if ($this->config->getToken() !== null) {
            $results[] = "--token={$this->config->getToken()}";
        }

        foreach ($this->config->getUrls() as $url) {
            $results[] = "--url={$url}";
        }

        if ($this->config->getUser() !== null) {
            $results[] = "--user={$this->config->getUser()}";
        }

        if ($this->config->isCors()) {
            $results[] = '--cors';
        }

        if ($this->config->isCorsReferer()) {
            $results[] = '--cors-referer';
        }

        if ($this->config->isEmptyProviderState()) {
            $results[] = '--empty-provider-state';
        }

        if ($this->config->isInsecureTls()) {
            $results[] = '--insecure-tls';
        }

        return $results;
    }
}
