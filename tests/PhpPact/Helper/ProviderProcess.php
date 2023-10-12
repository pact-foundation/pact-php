<?php

namespace PhpPactTest\Helper;

use Symfony\Component\Process\Process;

class ProviderProcess
{
    private Process $process;

    public function __construct(string $publicPath, private int $port = 7202)
    {
        $this->process = new Process(['php', '-S', "127.0.0.1:$port", '-t', $publicPath]);
    }

    public function start(): void
    {
        $this->process->start(function (string $type, string $buffer): void {
            echo "\n$type > $buffer";
        });
        $this->process->waitUntil(function (): bool {
            $fp = @fsockopen('127.0.0.1', $this->port);
            $isOpen = is_resource($fp);
            if ($isOpen) {
                fclose($fp);
            }

            return $isOpen;
        });
    }

    public function stop(): void
    {
        $this->process->stop();
    }
}
