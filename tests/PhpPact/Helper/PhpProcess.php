<?php

namespace PhpPactTest\Helper;

use PhpPactTest\Helper\Exception\NoPortAvailableException;
use Symfony\Component\Process\Process;

class PhpProcess extends AbstractProcess
{
    public function __construct(private string $publicPath, private int $port = 0)
    {
        parent::__construct();
    }

    public function getPort(): int
    {
        if (!$this->port) {
            $this->port = $this->findAvailablePort();
        }

        return $this->port;
    }

    protected function getProcess(): Process
    {
        return new Process(['php', '-S', '127.0.0.1:' . $this->getPort(), '-t', $this->publicPath]);
    }

    private function findAvailablePort(): int
    {
        $socket = \socket_create_listen(0);
        \socket_getsockname($socket, $addr, $port);
        \socket_close($socket);

        if (!$port) {
            throw new NoPortAvailableException();
        }

        return $port;
    }
}
