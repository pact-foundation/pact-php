<?php

namespace PhpPact\Standalone\MockService;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\HealthCheckFailedException;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use PhpPact\Standalone\Runner\ProcessRunner;

/**
 * Ruby Standalone Mock Server Wrapper
 * Class MockServer.
 */
class MockServer
{
    /** @var MockServerConfig */
    private $config;

    /** @var InstallManager */
    private $installManager;

    /** @var MockServerHttpService */
    private $httpService;

    /** @var ProcessRunner */
    private $processRunner;

    /**
     * MockServer constructor.
     *
     * @param MockServerConfigInterface  $config
     * @param null|MockServerHttpService $httpService
     */
    public function __construct(MockServerConfigInterface $config, MockServerHttpService $httpService = null)
    {
        $this->config         = $config;
        $this->installManager = new InstallManager();

        if (!$httpService) {
            $this->httpService = new MockServerHttpService(new GuzzleClient(), $this->config);
        } else {
            $this->httpService = $httpService;
        }
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
        $scripts = $this->installManager->install();

        $this->processRunner = new ProcessRunner($scripts->getMockService(), $this->getArguments());

        $processId =  $this->processRunner->run();

        $result = $this->verifyHealthCheck();
        if ($result) {
            $retrySec = $this->config->getHealthCheckRetrySec();
            \sleep($retrySec);
        }

        return $processId;
    }

    /**
     * Stop the Mock Server process.
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

        $log      = \escapeshellarg($this->config->getLog());
        $logLevel = $this->config->getLogLevel();

        $results[] = 'service';
        $results[] = "--consumer={$this->config->getConsumer()}";
        $results[] = "--provider={$this->config->getProvider()}";
        $results[] = "--pact-dir={$this->config->getPactDir()}";
        $results[] = "--pact-file-write-mode={$this->config->getPactFileWriteMode()}";
        $results[] = "--host={$this->config->getHost()}";
        $results[] = "--port={$this->config->getPort()}";
        $results[] = "--log={$log}";

        if ($logLevel) {
            $results[] = \sprintf('--log-level=%s', \escapeshellarg($logLevel));
        }

        if ($this->config->hasCors()) {
            $results[] = '--cors=true';
        }

        if ($this->config->getPactSpecificationVersion() !== null) {
            $results[] = "--pact-specification-version={$this->config->getPactSpecificationVersion()}";
        }

        if ($this->config->getLog() !== null) {
            $results[] = "--log={$this->config->getLog()}";
        }

        return $results;
    }

    /**
     * Make sure the server starts as expected.
     *
     * @throws Exception
     *
     * @return bool
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
                $status = $service->healthCheck();

                return $status;
            } catch (ConnectException $e) {
                \sleep($retrySec);
            }
        } while ($tries <= $maxTries);

        throw new HealthCheckFailedException("Failed to make connection to Mock Server in {$maxTries} attempts.");
    }
}
