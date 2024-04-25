<?php

namespace PhpPactTest\Standalone\StubServer;

use PhpPact\Standalone\StubService\Exception\LogLevelNotSupportedException;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\StubServerConfig;
use PhpPact\Standalone\StubService\StubServerConfigInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class StubServerTest extends TestCase
{
    protected StubServer $server;
    protected StubServerConfigInterface $config;
    protected Process&MockObject $process;

    public function setUp(): void
    {
        $this->process = $this->createMock(Process::class);
        $this->config = new StubServerConfig();
        $this->server = new StubServer($this->config, $this->process);
    }

    #[TestWith([null   , true])]
    #[TestWith(['trace', true])]
    #[TestWith(['debug', true])]
    #[TestWith(['info' , true])]
    #[TestWith(['warn' , true])]
    #[TestWith(['error', true])]
    #[TestWith(['none' , false])]
    public function testStart(?string $logLevel, bool $hasCallback): void
    {
        $this->config->setLogLevel($logLevel);
        $this->config->setPort(123);
        $this->process
            ->expects($this->once())
            ->method('start')
            ->with($hasCallback ? $this->callback(fn ($callback) => is_callable($callback)) : null);
        $this->process
            ->expects($this->once())
            ->method('getPid')
            ->willReturn($pid = 1234);
        $this->assertSame($pid, $this->server->start());
    }

    #[TestWith([null   ,  false])]
    #[TestWith(['trace',  false])]
    #[TestWith(['debug',  false])]
    #[TestWith(['info' ,  false])]
    #[TestWith([null   ,  true])]
    #[TestWith(['trace',  true])]
    #[TestWith(['debug',  true])]
    #[TestWith(['info' ,  true])]
    public function testStartWithSupportedLogLevelOnRandomPort(?string $logLevel, bool $started): void
    {
        $this->config->setLogLevel($logLevel);
        $this->config->setPort(0);
        $this->process
            ->expects($this->once())
            ->method('start');
        $this->process
            ->expects($this->once())
            ->method('getPid')
            ->willReturn($pid = 1234);
        $this->process
            ->expects($this->once())
            ->method('waitUntil')
            ->with($this->callback(function (callable $callback) use ($started) {
                $port = 123;
                $this->assertSame($started ? 1 : 0, $callback('out', $started ? "Server started on port $port" : 'not started'));
                $this->assertSame($started ? $port : 0, $this->config->getPort());

                return true;
            }));
        $this->assertSame($pid, $this->server->start());
    }

    #[TestWith(['warn'])]
    #[TestWith(['error'])]
    #[TestWith(['none'])]
    public function testStartWithUnsupportedLogLevelOnRandomPort(string $logLevel): void
    {
        $this->config->setLogLevel($logLevel);
        $this->config->setPort(0);
        $this->process
            ->expects($this->once())
            ->method('start');
        $this->process
            ->expects($this->never())
            ->method('getPid');
        $this->expectException(LogLevelNotSupportedException::class);
        $this->expectExceptionMessage("Setting random port for stub server required log level 'info', 'debug' or 'trace'. '$logLevel' given.");
        $this->server->start();
    }

    public function testStop(): void
    {
        $this->process
            ->expects($this->once())
            ->method('stop');
        $this->assertTrue($this->server->stop());
    }
}
