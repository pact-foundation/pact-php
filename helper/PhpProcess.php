<?php

namespace PhpPactTest\Helper;

use PhpPactTest\Helper\Exception\NoPortAvailableException;
use PhpPactTest\Helper\Exception\SocketNotOpenException;
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
        return new Process(['php', 'index.php'], $this->publicPath, ['X_LISTEN' => '127.0.0.1:' . $this->getPort()]);
    }

    private function findAvailablePort(): int
    {
        $socket = \socket_create_listen(0);
        if (is_bool($socket)) {
            throw new SocketNotOpenException(sprintf('Can not open socket: %s', socket_strerror(socket_last_error())));
        }
        \socket_getsockname($socket, $addr, $port);
        \socket_close($socket);

        if (!$port) {
            throw new NoPortAvailableException();
        }

        return $port;
    }
}
