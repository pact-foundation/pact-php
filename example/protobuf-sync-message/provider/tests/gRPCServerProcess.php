<?php

namespace ProtobufSyncMessageProvider\Tests;

use PhpPactTest\Helper\AbstractProcess;
use Symfony\Component\Process\Process;

class gRPCServerProcess extends AbstractProcess
{
    public function getPort(): int
    {
        return 50051;
    }

    protected function getProcess(): Process
    {
        $process = new Process(['php', 'index.php'], __DIR__ . '/../public/');
        $process->setTimeout(120);

        return $process;
    }
}
