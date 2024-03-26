<?php

namespace ProtobufSyncMessageProvider\Tests;

use PhpPactTest\Helper\AbstractProcess;
use Symfony\Component\Process\Process;

class RoadRunnerProcess extends AbstractProcess
{
    public function getPort(): int
    {
        return 9001;
    }

    protected function getProcess(): Process
    {
        $process = new Process([__DIR__ . '/../bin/roadrunner/rr', 'serve', '-w', __DIR__ . '/..']);
        $process->setTimeout(120);

        return $process;
    }
}
