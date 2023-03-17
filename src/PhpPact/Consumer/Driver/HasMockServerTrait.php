<?php

namespace PhpPact\Consumer\Driver;

use FFI;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

trait HasMockServerTrait
{
    protected function createMockServer(): void
    {
        $port = $this->ffi->pactffi_create_mock_server_for_transport(
            $this->pactId,
            $this->getMockServerConfig()->getHost(),
            $this->getMockServerConfig()->getPort(),
            $this->getMockServerTransport(),
            null
        );

        if ($port < 0) {
            throw new MockServerNotStartedException($port);
        }
        $this->getMockServerConfig()->setPort($port);
    }

    protected function mockServerMatched(): bool
    {
        $matched = $this->ffi->pactffi_mock_server_matched($this->getMockServerConfig()->getPort());

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    protected function cleanUpMockServer(): void
    {
        $this->ffi->pactffi_cleanup_mock_server($this->getMockServerConfig()->getPort());
    }

    abstract protected function getMockServerTransport(): string;

    abstract protected function getMockServerConfig(): MockServerConfigInterface;
}
