<?php

namespace PhpPact\Standalone\StubService;

use Exception;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\StubService\Exception\LogLevelNotSupportedException;
use Symfony\Component\Process\Process;

class StubServer
{
    private StubServerConfigInterface $config;

    private Process $process;

    public function __construct(StubServerConfigInterface $config, ?Process $process = null)
    {
        $this->config = $config;
        $this->process = $process ?? new Process([Scripts::getStubService(), ...$this->getArguments()], null, ['PACT_BROKER_BASE_URL' => false]);
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
        $logLevel = $this->config->getLogLevel();
        if (is_null($logLevel) || \strtoupper($logLevel) !== 'NONE') {
            $callback = function (string $type, string $buffer): void {
                echo "\n$type > $buffer";
            };
        }
        $this->process->start($callback ?? null);
        if (is_null($logLevel) || in_array(\strtoupper($logLevel), ['INFO', 'DEBUG', 'TRACE'])) {
            $this->process->waitUntil(function (string $type, string $output) {
                $result = preg_match('/Server started on port (\d+)/', $output, $matches);
                if ($result === 1 && $this->config->getPort() === 0) {
                    $this->config->setPort((int)$matches[1]);
                }

                return $result;
            });
        } else {
            if ($this->config->getPort() === 0) {
                throw new LogLevelNotSupportedException(sprintf("Setting random port for stub server required log level 'info', 'debug' or 'trace'. '%s' given.", $logLevel));
            }
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

        $results[] = "--port={$this->config->getPort()}";

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
