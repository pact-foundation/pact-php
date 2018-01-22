<?php

namespace PhpPact\Standalone\MockService;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use PhpPact\Consumer\Exception\HealthCheckFailedException;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Ruby Standalone Mock Server Wrapper
 * Class MockServer
 */
class MockServer
{
    /** @var MockServerConfig */
    private $config;

    /** @var InstallManager */
    private $installManager;

    /** @var Process */
    private $process;

    /** @var Filesystem */
    private $fileSystem;

    public function __construct(MockServerConfigInterface $config, InstallManager $installManager)
    {
        $this->config         = $config;
        $this->installManager = $installManager;
        $this->fileSystem     = new Filesystem();
    }

    /**
     * Start the Mock Server. Verify that it is running.
     *
     * @throws ProcessFailedException
     *
     * @return int process ID of the started Mock Server
     */
    public function start(): int
    {
        $scripts = $this->installManager->install();

        $builder       = new ProcessBuilder();
        $this->process = $builder
            ->setPrefix($scripts->getMockService())
            ->setArguments($this->buildParameters())
            ->getProcess()
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $this->process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                print 'ERR > ' . $buffer;
            } else {
                print 'OUT > ' . $buffer;
            }
        });
        \sleep(1);

        if ($this->process->isStarted() !== true || $this->process->isRunning() !== true) {
            throw new ProcessFailedException($this->process);
        }

        $this->verifyHealthCheck();

        return $this->process->getPid();
    }

    /**
     * Stop the Mock Server process.
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        $exitCode = $this->process->stop();
        print "Process exited with code {$exitCode}\n";

        return true;
    }

    /**
     * Build an array of command arguments.
     *
     * @return array
     */
    private function buildParameters(): array
    {
        $results = [];

        $results[] = 'service';
        $results[] = "--consumer={$this->config->getConsumer()}";
        $results[] = "--provider={$this->config->getProvider()}";
        $results[] = "--pact-dir={$this->config->getPactDir()}";
        $results[] = "--pact-file-write-mode={$this->config->getPactFileWriteMode()}";
        $results[] = "--host={$this->config->getHost()}";
        $results[] = "--port={$this->config->getPort()}";

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
        $service = new MockServerHttpService(new GuzzleClient(), $this->config);

        // Verify that the service is up.
        $tries    = 0;
        $maxTries = 10;
        do {
            $tries++;

            try {
                $status = $service->healthCheck();

                return $status;
            } catch (ConnectException $e) {
                \sleep(1);
            }
        } while ($tries <= $maxTries);

        throw new HealthCheckFailedException("Failed to make connection to Mock Server in {$maxTries} attempts.");
    }
}
