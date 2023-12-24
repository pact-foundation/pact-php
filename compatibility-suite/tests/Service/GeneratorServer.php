<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\Helper\ProviderProcess;

final class GeneratorServer implements GeneratorServerInterface
{
    private int $port = 0;
    private ProviderProcess $process;
    private string $bodyFile = Path::PUBLIC_PATH . '/generators/body.json';
    private string $pathFile = Path::PUBLIC_PATH . '/generators/path.txt';
    private string $headersFile = Path::PUBLIC_PATH . '/generators/headers.json';
    private string $queryParamsFile = Path::PUBLIC_PATH . '/generators/queryParams.json';

    public function start(): void
    {
        foreach ([$this->bodyFile, $this->pathFile, $this->headersFile, $this->queryParamsFile] as $file) {
            @unlink($file);
        }
        $socket = \socket_create_listen($this->port);
        \socket_getsockname($socket, $addr, $this->port);
        \socket_close($socket);
        $this->process = new ProviderProcess(Path::PUBLIC_PATH . '/generators/', $this->port);
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

    public function getBody(): string
    {
        return @file_get_contents($this->bodyFile);
    }

    public function getPath(): string
    {
        return file_get_contents($this->pathFile);
    }

    public function getHeader(string $header): array
    {
        $headers = json_decode(file_get_contents($this->headersFile), true);

        return $headers[$header] ?? [];
    }

    public function getQueryParam(string $name): string
    {
        $queryParams = json_decode(file_get_contents($this->queryParamsFile), true);

        return $queryParams[$name] ?? '';
    }
}
