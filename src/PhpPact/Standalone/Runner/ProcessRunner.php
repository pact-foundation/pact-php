<?php

namespace PhpPact\Standalone\Runner;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Process\ProcessException;
use Monolog\Logger;

/**
 * Wrapper around Process with Amp
 */
class ProcessRunner
{
    /** @var Process */
    private $process;

    /** @var string command output */
    private $output;

    /** @var int command exit code */
    private $exitCode;

    /** @var string */
    private $stderr;

    /**
     * @param string $command
     * @param array  $arguments
     */
    public function __construct(string $command, array $arguments)
    {
        $this->exitCode  = -1;
        $this->output    = null;
        $this->stderr    = null;
        $this->process   = new Process($command . ' ' . \implode(' ', $arguments));
    }

    /**
     * @return string
     */
    public function getOutput(): string
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
    public function getStderr(): string
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
        return $this->run(true);
    }

    /**
     * Run the process and set output
     *
     * @param bool $blocking
     *
     * @return int Process Id
     */
    public function run($blocking = false): int
    {
        $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
        $logHandler->setFormatter(new ConsoleFormatter);
        $logger = new Logger('server');
        $logger->pushHandler($logHandler);

        $pid        = null;
        $lambdaLoop = function () use ($blocking, $logger, &$pid) {
            $logger->debug("Process command: {$this->process->getCommand()}");

            $this->process->start();

            $this->process->getStdout()->read()->onResolve(function (\Throwable $reason = null, $value) {
                $this->output .= $value;
            });

            $this->process->getStderr()->read()->onResolve(function (\Throwable $reason = null, $value) {
                $this->stderr .= $value;
            });

            if ($blocking) {
                $exitCode = yield $this->process->join();
                $this->setExitCode($exitCode);
                $logger->debug("Exit code: {$this->getExitCode()}");
            }

            $pid = yield $this->process->getPid();

            if ($blocking) {
                if ($this->getExitCode() !== 0) {
                    throw new \Exception("PactPHP Process returned non-zero exit code: {$this->getExitCode()}");
                }
            }

            Loop::stop();
        };

        Loop::run($lambdaLoop);

        return $pid;
    }

    /**
     * Stop the running process
     *
     * @return bool
     */
    public function stop(): bool
    {
        $this->process->getPid()->onResolve(function ($error, $pid) {
            if ($error) {
                throw new ProcessException($error);
            }

            print "\nStopping Process Id: {$pid}\n";

            if ('\\' === \DIRECTORY_SEPARATOR) {
                \exec(\sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
                if ($exitCode) {
                    throw new ProcessException(\sprintf('Unable to kill the process (%s).', \implode(' ', $output)));
                }
            } else {
                $this->process->signal(15);

                if ($ok = \proc_open("kill $pid", [2 => ['pipe', 'w']], $pipes)) {
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
}
