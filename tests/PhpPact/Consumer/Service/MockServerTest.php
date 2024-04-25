<?php

namespace PhpPactTest\Consumer\Service;

use FFI;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\Exception\MockServerPactFileNotWrittenException;
use PhpPact\Consumer\Model\Pact\Pact;
use PhpPact\Consumer\Service\MockServer;
use PhpPact\Consumer\Service\MockServerInterface;
use PhpPact\FFI\ClientInterface;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPactTest\Helper\FFI\ClientTrait;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockServerTest extends TestCase
{
    use ClientTrait;

    protected MockServerInterface $mockServer;
    protected PactDriverInterface&MockObject $pactDriver;
    protected MockServerConfigInterface $config;
    protected int $pactHandle = 123;
    protected string $host = 'example.test';
    protected int $port = 123;
    protected string $pactDir = '/path/to/pact/dir';

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->pactDriver = $this->createMock(PactDriverInterface::class);
        $this->config = new MockServerConfig();
        $this->mockServer = new MockServer($this->client, $this->pactDriver, $this->config);
    }

    #[TestWith([234, true])]
    #[TestWith([234, false])]
    #[TestWith([0, true])]
    #[TestWith([-1, true])]
    #[TestWith([-2, true])]
    #[TestWith([-3, true])]
    #[TestWith([-4, true])]
    #[TestWith([-5, true])]
    #[TestWith([-6, true])]
    public function testStart(int $returnedPort, bool $secure): void
    {
        $this->config->setHost($this->host);
        $this->config->setPort($this->port);
        $this->config->setSecure($secure);
        $this->pactDriver
            ->expects($this->once())
            ->method('getPact')
            ->willReturn(new Pact($this->pactHandle));
        $calls = [
            ['pactffi_create_mock_server_for_transport', $this->pactHandle, $this->host, $this->port, $this->getTransport($secure), null, $returnedPort],
        ];
        $this->assertClientCalls($calls);
        if ($returnedPort < 0) {
            $this->expectException(MockServerNotStartedException::class);
            $this->expectExceptionMessage(match ($returnedPort) {
                -1 => 'An invalid handle was received. Handles should be created with `pactffi_new_pact`',
                -2 => 'Transport_config is not valid JSON',
                -3 => 'The mock server could not be started',
                -4 => 'The method panicked',
                -5 => 'The address is not valid',
                default => 'Unknown error',
            });
        }
        $this->mockServer->start();
        $this->assertSame($returnedPort, $this->config->getPort());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerify(bool $matched): void
    {
        $this->config->setPort($this->port);
        $this->config->setPactDir($this->pactDir);
        $calls = $matched ? [
            ['pactffi_mock_server_matched', $this->port, $matched],
            ['pactffi_write_pact_file', $this->port, $this->pactDir, false, 0],
            ['pactffi_cleanup_mock_server', $this->port, null],
        ] : [
            ['pactffi_mock_server_matched', $this->port, $matched],
            ['pactffi_mock_server_mismatches', $this->port, FFI::new('char[1]')],
            ['pactffi_cleanup_mock_server', $this->port, null],
        ];
        $this->assertClientCalls($calls);
        $this->pactDriver
            ->expects($this->once())
            ->method('cleanUp');
        $result = $this->mockServer->verify();
        $this->assertSame($matched, $result->matched);
        $this->assertSame('', $result->mismatches);
    }

    #[TestWith([0, PactConfigInterface::MODE_OVERWRITE])]
    #[TestWith([1, PactConfigInterface::MODE_OVERWRITE])]
    #[TestWith([2, PactConfigInterface::MODE_OVERWRITE])]
    #[TestWith([3, PactConfigInterface::MODE_OVERWRITE])]
    #[TestWith([4, PactConfigInterface::MODE_OVERWRITE])]
    #[TestWith([0, PactConfigInterface::MODE_MERGE])]
    #[TestWith([1, PactConfigInterface::MODE_MERGE])]
    #[TestWith([2, PactConfigInterface::MODE_MERGE])]
    #[TestWith([3, PactConfigInterface::MODE_MERGE])]
    #[TestWith([4, PactConfigInterface::MODE_MERGE])]
    public function testWritePact(int $error, string $writeMode): void
    {
        $this->config->setPort($this->port);
        $this->config->setPactDir($this->pactDir);
        $this->config->setPactFileWriteMode($writeMode);
        $calls = [
            ['pactffi_write_pact_file', $this->port, $this->pactDir, $writeMode === PactConfigInterface::MODE_OVERWRITE, $error],
        ];
        $this->assertClientCalls($calls);
        if ($error) {
            $this->expectException(MockServerPactFileNotWrittenException::class);
            $this->expectExceptionMessage(match ($error) {
                1 => 'A general panic was caught',
                2 => 'The pact file was not able to be written',
                3 => 'A mock server with the provided port was not found',
                default => 'Unknown error',
            });
        }
        $this->mockServer->writePact();
    }

    public function testCleanUp(): void
    {
        $this->config->setPort($this->port);
        $calls = [
            ['pactffi_cleanup_mock_server', $this->port, null],
        ];
        $this->assertClientCalls($calls);
        $this->pactDriver
            ->expects($this->once())
            ->method('cleanUp');
        $this->mockServer->cleanUp();
    }

    protected function getTransport(bool $secure): string
    {
        return $secure ? 'https' : 'http';
    }
}
