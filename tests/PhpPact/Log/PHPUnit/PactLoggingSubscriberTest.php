<?php

namespace PhpPactTest\Log\PHPUnit;

use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\LoggerInterface;
use PhpPact\Log\Model\File;
use PhpPact\Log\Model\SinkInterface;
use PhpPact\Log\Model\Stdout;
use PhpPact\Log\PHPUnit\PactLoggingSubscriber;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Runtime\Runtime;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PactLoggingSubscriberTest extends TestCase
{
    private LoggerInterface&MockObject $logger;
    private PactLoggingSubscriber $subscriber;
    private Started $event;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new PactLoggingSubscriber($this->logger);
        $this->event = new Started(
            new Info(
                new Snapshot(HRTime::fromSecondsAndNanoseconds(0, 0), MemoryUsage::fromBytes(0), MemoryUsage::fromBytes(0), new GarbageCollectorStatus(0, 0, 0, 0, null, null, null, null, null, null, null, null)),
                Duration::fromSecondsAndNanoseconds(0, 0),
                MemoryUsage::fromBytes(0),
                Duration::fromSecondsAndNanoseconds(0, 0),
                MemoryUsage::fromBytes(0)
            ),
            new Runtime()
        );
    }

    public function testDoNotLog(): void
    {
        putenv('PACT_LOG');
        putenv('PACT_LOGLEVEL');
        $this->logger
            ->expects($this->never())
            ->method('attach');
        $this->logger
            ->expects($this->never())
            ->method('apply');
        $this->subscriber->notify($this->event);
    }

    public function testLogToFile(): void
    {
        putenv('PACT_LOG=./log/pact.txt');
        putenv('PACT_LOGLEVEL=trace');
        $this->logger
            ->expects($this->once())
            ->method('attach')
            ->with($this->callback(function (SinkInterface $sink) {
                $this->assertInstanceOf(File::class, $sink);
                $this->assertSame('file ./log/pact.txt', $sink->getSpecifier());
                $this->assertSame(LogLevel::TRACE, $sink->getLevel());

                return true;
            }));
        $this->logger
            ->expects($this->once())
            ->method('apply');
        $this->subscriber->notify($this->event);
    }

    public function testLogToFileWithDefaultLevel(): void
    {
        putenv('PACT_LOG=./log/pact.txt');
        putenv('PACT_LOGLEVEL');
        $this->logger
            ->expects($this->once())
            ->method('attach')
            ->with($this->callback(function (SinkInterface $sink) {
                $this->assertInstanceOf(File::class, $sink);
                $this->assertSame('file ./log/pact.txt', $sink->getSpecifier());
                $this->assertSame(LogLevel::INFO, $sink->getLevel());

                return true;
            }));
        $this->logger
            ->expects($this->once())
            ->method('apply');
        $this->subscriber->notify($this->event);
    }

    public function testLogToStandardOutput(): void
    {
        putenv('PACT_LOG');
        putenv('PACT_LOGLEVEL=debug');
        $this->logger
            ->expects($this->once())
            ->method('attach')
            ->with($this->callback(function (SinkInterface $sink) {
                $this->assertInstanceOf(Stdout::class, $sink);
                $this->assertSame('stdout', $sink->getSpecifier());
                $this->assertSame(LogLevel::DEBUG, $sink->getLevel());

                return true;
            }));
        $this->logger
            ->expects($this->once())
            ->method('apply');
        $this->subscriber->notify($this->event);
    }
}
