<?php

namespace PhpPact\Standalone\MockService;

use Amp\Process\ProcessException;
use Exception;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\HealthCheckFailedException;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use PhpPact\Standalone\Runner\ProcessRunner;
use PhpPact\Exception\ConnectionException;

/**
 * Ruby Standalone Mock Server Wrapper
 */
class MockServer
{
    private MockServerConfig $config;

    private MockServerHttpService $httpService;

    private ProcessRunner $processRunner;

    public function __construct(MockServerConfig $config, MockServerHttpService $httpService = null)
    {
        $this->config = $config;
        $this->httpService = $httpService ?: new MockServerHttpService(new GuzzleClient(), $this->config);
    }

    /**
     * Start the Mock Server. Verify that it is running.
     *
     * @throws Exception
     *
     * @return int process ID of the started Mock Server
     */
    public function start(): int
    {
        $this->processRunner = new ProcessRunner(Scripts::getMockService(), $this->getArguments());

        $processId =  $this->processRunner->run();

        $result = $this->verifyHealthCheck();

        return $processId;
    }

    /**
     * Stop the Mock Server process.
     *
     * @return bool Was stopping successful?
     * @throws ProcessException
     */
    public function stop(): bool
    {
        return $this->processRunner->stop();
    }

    /**
     * Build an array of command arguments.
     *
     * @return array<int, string>
     */
    private function getArguments(): array
    {
        $results = [];

        $logLevel = $this->config->getLogLevel();
        $consumer = \escapeshellarg($this->config->getConsumer());
        $provider = \escapeshellarg($this->config->getProvider());
        $pactDir  = \escapeshellarg($this->config->getPactDir());

        $results[] = 'service';
        $results[] = "--consumer={$consumer}";
        $results[] = "--provider={$provider}";
        $results[] = "--pact-dir={$pactDir}";
        $results[] = "--pact-file-write-mode={$this->config->getPactFileWriteMode()}";
        $results[] = "--host={$this->config->getHost()}";
        $results[] = "--port={$this->config->getPort()}";

        if ($logLevel !== null) {
            $results[] = \sprintf('--log-level=%s', \escapeshellarg($logLevel));
        }

        if ($this->config->hasCors()) {
            $results[] = '--cors=true';
        }

        if ($this->config->getPactSpecificationVersion() !== null) {
            $results[] = "--pact-specification-version={$this->config->getPactSpecificationVersion()}";
        }

        if ($this->config->getLog() !== null) {
            $log       = \escapeshellarg($this->config->getLog());
            $results[] = \sprintf('--log=%s', $log);
        }

        return $results;
    }

    /**
     * Make sure the server starts as expected.
     *
     * @throws Exception
     */
    private function verifyHealthCheck(): bool
    {
        $service = $this->httpService;

        // Verify that the service is up.
        $tries    = 0;
        $maxTries = $this->config->getHealthCheckTimeout();
        $retrySec = $this->config->getHealthCheckRetrySec();
        do {
            ++$tries;

            try {
                return $service->healthCheck();
            } catch (ConnectionException $e) {
                \usleep(round($retrySec * 1000000));
            }
        } while ($tries <= $maxTries);

        throw new HealthCheckFailedException("Failed to make connection to Mock Server in {$maxTries} attempts.");
    }
}
