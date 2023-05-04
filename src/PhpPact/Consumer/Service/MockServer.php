<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Exception\MockServerNotWrotePactFileException;
use PhpPact\Consumer\Service\Helper\FFITrait;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

class MockServer implements MockServerInterface
{
    use FFITrait;

    public function __construct(
        private PactRegistryInterface $pactRegistry,
        private MockServerConfigInterface $config
    ) {
        $this->createFFI();
    }

    public function init(): int
    {
        $this->pactRegistry->registerPact();

        return $this->pactRegistry->getId();
    }

    public function start(): void
    {
        $port = $this->ffi->pactffi_create_mock_server_for_transport(
            $this->pactRegistry->getId(),
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

    public function isMatched(): bool
    {
        return $this->ffi->pactffi_mock_server_matched($this->config->getPort());
    }

    public function writePact(): void
    {
        $error = $this->ffi->pactffi_write_pact_file(
            $this->config->getPort(),
            $this->config->getPactDir(),
            $this->config->getPactFileWriteMode() === PactConfigInterface::MODE_OVERWRITE
        );
        if ($error) {
            throw new MockServerNotWrotePactFileException($error);
        }
    }

    public function cleanUp(): void
    {
        $this->ffi->pactffi_cleanup_mock_server($this->config->getPort());
        $this->pactRegistry->cleanUp();
    }

    protected function getTransport(): string
    {
        return $this->config->isSecure() ? 'https' : 'http';
    }

    protected function getTransportConfig(): ?string
    {
        return null;
    }
}
