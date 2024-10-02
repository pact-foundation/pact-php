<?php

namespace PhpPactTest\Consumer\Service;

use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Driver\Pact\PactDriverInterface;
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
        $this->expectsCreateMockServerForTransport($this->pactHandle, $this->host, $this->port, $this->getTransport($secure), null, $returnedPort);
        $this->mockServer->start();
        $this->assertSame($returnedPort, $this->config->getPort());
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testVerify(bool $matched): void
    {
        $this->config->setPort($this->port);
        $this->config->setPactDir($this->pactDir);
        $this->expectsMockServerMatched($this->port, $matched);
        $this->expectsWritePactFile($this->port, $this->pactDir, false, 0, $matched);
        $this->expectsMockServerMismatches($this->port, '', $matched);
        $this->expectsCleanupMockServer($this->port, true);
        $this->pactDriver
            ->expects($this->once())
            ->method('cleanUp');
        $result = $this->mockServer->verify();
        $this->assertSame($matched, $result->matched);
        $this->assertSame('', $result->mismatches);
    }

    #[TestWith([0, WriteMode::OVERWRITE])]
    #[TestWith([1, WriteMode::OVERWRITE])]
    #[TestWith([2, WriteMode::OVERWRITE])]
    #[TestWith([3, WriteMode::OVERWRITE])]
    #[TestWith([4, WriteMode::OVERWRITE])]
    #[TestWith([0, WriteMode::MERGE])]
    #[TestWith([1, WriteMode::MERGE])]
    #[TestWith([2, WriteMode::MERGE])]
    #[TestWith([3, WriteMode::MERGE])]
    #[TestWith([4, WriteMode::MERGE])]
    public function testWritePact(int $error, WriteMode $writeMode): void
    {
        $this->config->setPort($this->port);
        $this->config->setPactDir($this->pactDir);
        $this->config->setPactFileWriteMode($writeMode);
        $this->expectsWritePactFile($this->port, $this->pactDir, $writeMode === WriteMode::OVERWRITE, $error, true);
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

    #[TestWith([true])]
    #[TestWith([false])]
    public function testCleanUp(bool $success): void
    {
        $this->config->setPort($this->port);
        $this->expectsCleanupMockServer($this->port, $success);
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
