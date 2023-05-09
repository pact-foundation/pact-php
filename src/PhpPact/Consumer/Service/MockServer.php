<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Exception\MockServerNotWrotePactFileException;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class MockServer implements MockServerInterface
{
    public function __construct(
        private ClientInterface $client,
        private PactDriverInterface $pactDriver,
        private MockServerConfigInterface $config
    ) {
    }

    public function start(): void
    {
        $port = $this->client->call(
            'pactffi_create_mock_server_for_transport',
            $this->pactDriver->getId(),
            $this->config->getHost(),
            $this->config->getPort(),
            $this->getTransport(),
            $this->getTransportConfig()
        );

        if ($port < 0) {
            throw new MockServerNotStartedException($port);
        }
        $this->config->setPort($port);
    }

    public function verify(): bool
    {
        $matched = $this->client->call('pactffi_mock_server_matched', $this->config->getPort());

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    protected function getTransport(): string
    {
        return $this->config->isSecure() ? 'https' : 'http';
    }

    protected function getTransportConfig(): ?string
    {
        return null;
    }

    private function writePact(): void
    {
        $error = $this->client->call(
            'pactffi_write_pact_file',
            $this->config->getPort(),
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new MockServerNotWrotePactFileException($error);
        }
    }

    private function cleanUp(): void
    {
        $this->client->call('pactffi_cleanup_mock_server', $this->config->getPort());
        $this->pactDriver->cleanUp();
    }
}
