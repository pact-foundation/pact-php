<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\Helper\ProviderProcess;

final class ProviderStateServer implements ProviderStateServerInterface
{
    private int $port = 0;
    private ProviderProcess $process;

    public function start(): void
    {
        @unlink(Path::PUBLIC_PATH . '/provider-states/provider-states.json');
        $socket = \socket_create_listen($this->port);
        \socket_getsockname($socket, $addr, $this->port);
        \socket_close($socket);
        $this->process = new ProviderProcess(Path::PUBLIC_PATH . '/provider-states/', $this->port);
        $this->process->start();
    }

    public function stop(): void
    {
        $this->port = 0;
        $this->process->stop();
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function hasAction(string $action): bool
    {
        return file_get_contents("http://localhost:$this->port/has-action?action=" . urlencode($action));
    }

    public function hasState(string $action, string $state, array $params = []): bool
    {
        return file_get_contents("http://localhost:$this->port/has-state?action=" . urlencode($action) . '&state=' . urlencode($state) . ($params ? ('&' . http_build_query($params)) : ''));
    }
}
