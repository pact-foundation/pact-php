<?php

namespace PhpPact\Standalone\MockService;

use Amp\Process\Process;
use Amp\Process\ProcessException;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\HealthCheckFailedException;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Service\InstallerInterface;
use PhpPact\Standalone\MockService\Service\MockServerHttpService;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

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

    /** @var Process */
    private $process;

    /** @var Filesystem */
    private $fileSystem;

    /** @var ConsoleOutput */
    private $console;

    /** @var MockServerHttpService */
    private $httpService;

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
        $this->fileSystem     = new Filesystem();
        $this->console        = new ConsoleOutput();

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

        $processId = null;

        \Amp\Loop::run(function () use ($scripts, &$processId) {
            $this->process = new Process($scripts->getMockService() . ' ' . \implode(' ', $this->getArguments()));

            $this->console->writeln("Starting the mock service with command {$this->process->getCommand()}");
            $this->process->start();

            $processId = yield $this->process->getPid();

            $stream = $this->process->getStdout();
            $this->console->write(yield $stream->read());

            if (!$this->process->isRunning()) {
                throw new ProcessException('Failed to start mock server');
            }

            \Amp\Loop::delay($msDelay = 100, 'Amp\\Loop::stop');
        });

        $this->verifyHealthCheck();

        return $processId;
    }

    /**
     * Stop the Mock Server process.
     *
     * @throws ProcessException
     *
     * @return bool Was stopping successful?
     */
    public function stop(): bool
    {
        $this->process->kill();

        $this->process->getPid()->onResolve(function ($error, $pid) {
            if ($error) {
                throw new ProcessException($error);
            }

            print "\nStopping Process Id: {$pid}\n";
            $this->process->signal(15);

            if ('\\' === \DIRECTORY_SEPARATOR) {
                \exec(\sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
                if ($exitCode) {
                    throw new ProcessException(\sprintf('Unable to kill the process (%s).', \implode(' ', $output)));
                }
            } else {
                if ($ok = \proc_open("kill -9 $pid", [2 => ['pipe', 'w']], $pipes)) {
                    $ok = false === \fgets($pipes[2]);
                }

                if (!$ok) {
                    throw new ProcessException(\sprintf('Error while killing process "%s".', $pid));
                }
            }

            $this->process->kill();
        });

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

        $results[] = 'service';
        $results[] = "--consumer={$this->config->getConsumer()}";
        $results[] = "--provider={$this->config->getProvider()}";
        $results[] = "--pact-dir={$this->config->getPactDir()}";
        $results[] = "--pact-file-write-mode={$this->config->getPactFileWriteMode()}";
        $results[] = "--host={$this->config->getHost()}";
        $results[] = "--port={$this->config->getPort()}";

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
        do {
            ++$tries;

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
