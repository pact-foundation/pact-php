<?php

namespace PhpPact\Provider\Proxy;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\Exception\HealthCheckFailedException;
use PhpPact\Standalone\Runner\ProcessRunner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class HttpServer
{
    /** @var ProxyServerConfig */
    private $config;

    /** @var Process */
    private $process;

    /** @var ConsoleOutput */
    private $console;

    public function __construct(ProxyServerConfig $config)
    {
        $this->config         = $config;
        $this->console        = new ConsoleOutput();
    }

    /**
     * Start the Proxy Server. Verify that it is running.
     *
     * @throws HealthCheckFailedException
     * @throws \Exception
     *
     * @return int
     */
    public function start(): int
    {
        $this->process = ProcessRunner::run($this->getCommand(), $this->getArguments());
        $this->process
            ->setTimeout(600)
            ->setIdleTimeout(60);

        $this->console->writeln("Starting the proxy service with command {$this->process->getCommandLine()}.");

        $this->process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->console->write($buffer);
            } else {
                $this->console->write($buffer);
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
        $this->console->writeln("Process exited with code {$exitCode}.");

        return true;
    }

    /**
     * Make sure the proxy server starts as expected.
     *
     * @throws HealthCheckFailedException
     * @throws \Exception
     *
     * @return bool
     */
    private function verifyHealthCheck(): bool
    {
        $tries    = 0;
        $maxTries = 10;
        do {
            ++$tries;

            try {
                $status = $this->executeHealthCheck();

                return $status;
            } catch (ConnectException $e) {
                \sleep(1);
            }
        } while ($tries <= $maxTries);

        throw new HealthCheckFailedException("Failed to make connection to Mock Server in {$maxTries} attempts.");
    }

    /**
     * @throws Exception
     *
     * @return bool
     */
    private function executeHealthCheck(): bool
    {
        $client = new GuzzleClient();

        $uri = (new Uri("http://{$this->config->getHost()}:{$this->config->getPort()}"))
                ->withPath('/health');

        $response = $client->get($uri, []);

        $body = $response->getBody()->getContents();
        $json = \json_decode($body);

        if ($response->getStatusCode() !== 200
            || $json->status !== 'OK') {
            throw new \Exception('Failed to receive a successful response from the Proxy Server.');
        }

        return true;
    }

    /**
     * php -S localhost:8080 -t public index.php
     *
     * @return string
     */
    private function getCommand(): string
    {
        return $this->config->getPhpExe();
    }

    /**
     * Build an array of command arguments.
     *
     * @return array
     */
    private function getArguments(): array
    {
        $results = [];

        $results[] = '-S';
        $results[] = "{$this->config->getHost()}:{$this->config->getPort()}";
        $results[] = '-t';
        $results[] = "{$this->config->getRootDir()}";
        $results[] = 'index.php';

        return $results;
    }
}
