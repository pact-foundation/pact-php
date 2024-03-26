<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\Helper\PhpProcess;

final class ProviderStateServer implements ProviderStateServerInterface
{
    private PhpProcess $process;

    public function start(): void
    {
        @unlink(Path::PUBLIC_PATH . '/provider-states/provider-states.json');
        $this->process = new PhpProcess(Path::PUBLIC_PATH . '/provider-states/');
        $this->process->start();
    }

    public function stop(): void
    {
        $this->process->stop();
    }

    public function getPort(): int
    {
        return $this->process->getPort();
    }

    public function hasAction(string $action): bool
    {
        return file_get_contents(sprintf('http://localhost:%d/has-action?action=%s', $this->getPort(), urlencode($action)));
    }

    public function hasState(string $action, string $state, array $params = []): bool
    {
        return file_get_contents(sprintf('http://localhost:%d/has-state?action=%s&state=%s%s', $this->getPort(), urlencode($action), urlencode($state), $params ? ('&' . http_build_query($params)) : ''));
    }
}
