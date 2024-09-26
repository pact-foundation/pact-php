<?php

namespace PhpPactTest\Helper;

use Symfony\Component\Process\Process;

abstract class AbstractProcess
{
    private Process $process;

    public function __construct()
    {
        $this->process = $this->getProcess();
    }

    public function start(): void
    {
        if ($this->process->isRunning()) {
            return;
        }
        if (($logLevel = \getenv('PACT_LOGLEVEL')) && !in_array(\strtoupper($logLevel), ['OFF', 'NONE'])) {
            $callback = function (string $type, string $buffer): void {
                echo "\n$type > $buffer";
            };
        }
        $this->process->start($callback ?? null);
        $this->process->waitUntil(function (): bool {
            $fp = @fsockopen('127.0.0.1', $this->getPort());
            $isOpen = \is_resource($fp);
            if ($isOpen) {
                \fclose($fp);
            }

            return $isOpen;
        });
    }

    public function stop(): void
    {
        $this->process->stop();
    }

    abstract public function getPort(): int;

    abstract protected function getProcess(): Process;
}
