<?php

namespace PhpPact\Standalone\Runner;

use Amp\ByteStream;
use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Process\ProcessException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Wrapper around Process with Amp
 */
class ProcessRunner
{
    private Process $process;
    private ?string $output = null;
    private int $exitCode;
    private ?string $stderr          = null;
    private ?LoggerInterface $logger = null;

    /**
     * @param string $command
     * @param array  $arguments
     */
    public function __construct(string $command, array $arguments)
    {
        $this->exitCode  = -1;
        $this->process   = new Process($command . ' ' . \implode(' ', $arguments));
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return ProcessRunner
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @param int $exitCode
     */
    public function setExitCode(int $exitCode): void
    {
        $this->exitCode = $exitCode;
    }

    public function getCommand(): string
    {
        return $this->process->getCommand();
    }

    /**
     * @return string
     */
    public function getStderr(): ?string
    {
        return $this->stderr;
    }

    /**
     * @param string $stderr
     */
    public function setStderr(string $stderr): void
    {
        $this->stderr = $stderr;
    }

    /**
     * Run a blocking, synchronous process
     */
    public function runBlocking(): int
    {
        $logger     = $this->getLogger();
        $pid        = null;
        $lambdaLoop = function () use ($logger, &$pid) {
            $logger->debug("Process command: {$this->process->getCommand()}");

            $pid = yield $this->process->start();

            $this->output .= yield ByteStream\buffer($this->process->getStdout());
            $this->stderr .= yield ByteStream\buffer($this->process->getStderr());

            $exitCode = yield $this->process->join();
            $this->setExitCode($exitCode);
            $logger->debug("Exit code: {$this->getExitCode()}");

            if ($this->getExitCode() !== 0) {
                throw new \Exception("PactPHP Process returned non-zero exit code: {$this->getExitCode()}");
            }

            Loop::stop();
        };

        Loop::run($lambdaLoop);

        return $pid;
    }

    /**
     * Run a blocking, synchronous process
     */
    public function runNonBlocking(): int
    {
        $logger     = $this->getLogger();

        $pid        = null;

        $lambdaLoop = function () use ($logger, &$pid) {
            $logger->debug("start background command: {$this->process->getCommand()}");

            $pid = yield $this->process->start();

            $this->process->getStdout()->read()->onResolve(function (Throwable $reason = null, $value) {
                $this->output .= $value;
            });
            $this->process->getStderr()->read()->onResolve(function (Throwable $reason = null, $value) {
                $this->output .= $value;
            });

            Loop::stop();
        };

        Loop::run($lambdaLoop);

        $logger->debug("started process pid=$pid");

        return $pid;
    }

    /**
     * Run the process and set output
     *
     * @param bool $blocking
     *
     * @return int Process Id
     */
    public function run(bool $blocking = false): int
    {
        return $blocking
            ? $this->runBlocking()
            : $this->runNonBlocking();
    }

    /**
     * Stop the running process
     *
     * @throws ProcessException
     *
     * @return bool
     */
    public function stop(): bool
    {
        $pid = $this->process->getPid();

        print "\nStopping Process Id: {$pid}\n";

        if ('\\' === \DIRECTORY_SEPARATOR) {
            \exec(\sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
        }

        $this->process->kill();

        if ($this->process->isRunning()) {
            throw new ProcessException(\sprintf('Error while killing process "%s".', $pid));
        }

        return true;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        if (null === $this->logger) {
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter(null, null, true));
            $this->logger = new Logger('server');
            $this->logger->pushHandler($logHandler);
        }

        return $this->logger;
    }
}
