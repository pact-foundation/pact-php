<?php

namespace PhpPactTest\Log;

use Error;
use PhpPact\FFI\ClientInterface;
use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Exception\LoggerApplyException;
use PhpPact\Log\Exception\LoggerAttachSinkException;
use PhpPact\Log\Exception\LoggerUnserializeException;
use PhpPact\Log\Logger;
use PhpPact\Log\LoggerInterface;
use PhpPact\Log\Model\Buffer;
use PhpPact\Log\Model\File;
use PhpPact\Log\Model\Stderr;
use PhpPact\Log\Model\Stdout;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    protected ClientInterface&MockObject $client;
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterTrace')
            ->willReturn(5);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterDebug')
            ->willReturn(4);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterInfo')
            ->willReturn(3);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterWarn')
            ->willReturn(2);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterError')
            ->willReturn(1);
        $this->client
            ->expects($this->any())
            ->method('getLevelFilterOff')
            ->willReturn(0);
        $this->logger = Logger::instance($this->client);
    }

    public function tearDown(): void
    {
        Logger::tearDown();
    }

    public function testSameInstance(): void
    {
        $this->assertSame(Logger::instance($this->client), $this->logger);
    }

    public function testClone(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Call to protected PhpPact\Log\Logger::__clone()');
        // @phpstan-ignore expr.resultUnused
        clone $this->logger;
    }

    public function testSerialize(): void
    {
        $this->expectException(LoggerUnserializeException::class);
        $this->expectExceptionMessage('Cannot unserialize a singleton.');
        $result = serialize($this->logger);
        unserialize($result);
    }

    public function testApply(): void
    {
        $this->logger->attach(new File('/path/to/file', LogLevel::DEBUG));
        $this->logger->attach(new Buffer(LogLevel::ERROR));
        $this->logger->attach(new Stdout(LogLevel::WARN));
        $this->logger->attach(new Stderr(LogLevel::INFO));
        $calls = [
            [
                'args' => ['file /path/to/file', 4],
                'return' => 0
            ],
            [
                'args' => ['buffer', 1],
                'return' => 0
            ],
            [
                'args' => ['stdout', 2],
                'return' => 0
            ],
            [
                'args' => ['stderr', 3],
                'return' => 0
            ]
        ];
        $this->client
            ->expects($this->exactly(4))
            ->method('loggerAttachSink')
            ->willReturnCallback(function (...$args) use (&$calls) {
                $call = array_shift($calls);
                $this->assertSame($call['args'], $args);

                return $call['return'];
            });
        $this->client
            ->expects($this->once())
            ->method('loggerApply')
            ->willReturn(0);
        $this->logger->apply();
        $this->logger->attach(new Stderr(LogLevel::TRACE));
        $this->logger->apply();
        $this->logger->apply();
    }

    #[TestWith([0])]
    #[TestWith([-1])]
    #[TestWith([-2])]
    #[TestWith([-3])]
    #[TestWith([-4])]
    #[TestWith([-5])]
    #[TestWith([-6])]
    #[TestWith([-7])]
    public function testAttachSinkReturnError(int $error): void
    {
        $this->logger->attach(new Stderr(LogLevel::INFO));
        $this->client
            ->expects($this->once())
            ->method('loggerAttachSink')
            ->with('stderr', 3)
            ->willReturn($error);
        if ($error) {
            $this->expectException(LoggerAttachSinkException::class);
            $this->expectExceptionMessage(match ($error) {
                -1 => "Can't set logger (applying the logger failed, perhaps because one is applied already).",
                -2 => 'No logger has been initialized (call `pactffi_logger_init` before any other log function).',
                -3 => 'The sink specifier was not UTF-8 encoded.',
                -4 => 'The sink type specified is not a known type (known types: "stdout", "stderr", "buffer", or "file /some/path").',
                -5 => 'No file path was specified in a file-type sink specification.',
                -6 => 'Opening a sink to the specified file path failed (check permissions).',
                default => 'Unknown error',
            });
        }
        $this->logger->apply();
    }

    #[TestWith([0])]
    #[TestWith([-1])]
    #[TestWith([-2])]
    public function testApplyReturnError(int $error): void
    {
        $this->logger->attach(new Stderr(LogLevel::OFF));
        $this->client
            ->expects($this->once())
            ->method('loggerAttachSink')
            ->with('stderr', 0)
            ->willReturn(0);
        $this->client
            ->expects($this->once())
            ->method('loggerApply')
            ->willReturn($error);
        if ($error) {
            $this->expectException(LoggerApplyException::class);
            $this->expectExceptionMessage(match ($error) {
                -1 => "Can't set logger (applying the logger failed, perhaps because one is applied already).",
                default => 'Unknown error',
            });
        }
        $this->logger->apply();
    }

    public function testFetchBuffer(): void
    {
        $log = 'log from pact';
        $this->client
            ->expects($this->once())
            ->method('fetchLogBuffer')
            ->willReturn($log);
        $this->assertSame($log, $this->logger->fetchBuffer());
    }
}
