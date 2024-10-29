<?php

namespace PhpPact\Log;

use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Exception\LoggerApplyException;
use PhpPact\Log\Exception\LoggerAttachSinkException;
use PhpPact\Log\Exception\LoggerUnserializeException;
use PhpPact\Log\Model\SinkInterface;

final class Logger implements LoggerInterface
{
    private static ?self $instance = null;
    /**
     * @var SinkInterface[]
     */
    private array $sinks = [];
    private bool $applied = false;

    protected function __construct(private ClientInterface $client)
    {
        $this->client->loggerInit();
    }

    protected function __clone(): void
    {
    }

    public function __wakeup(): never
    {
        throw new LoggerUnserializeException('Cannot unserialize a singleton.');
    }

    public static function instance(?ClientInterface $client = null): Logger
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($client ?? new Client());
        }
        return self::$instance;
    }

    public static function tearDown(): void
    {
        self::$instance = null;
    }

    public function attach(SinkInterface $sink): void
    {
        if ($this->applied) {
            return;
        }
        $this->sinks[] = $sink;
    }

    public function apply(): void
    {
        if ($this->applied) {
            return;
        }
        foreach ($this->sinks as $sink) {
            $error = $this->client->loggerAttachSink($sink->getSpecifier(), $this->getLevelFilter($sink->getLevel()));
            if ($error) {
                throw new LoggerAttachSinkException($error);
            }
        }
        $error = $this->client->loggerApply();
        if ($error) {
            throw new LoggerApplyException($error);
        }
        $this->applied = true;
    }

    public function fetchBuffer(): string
    {
        return $this->client->fetchLogBuffer();
    }

    private function getLevelFilter(LogLevel $level): int
    {
        return match ($level) {
            LogLevel::TRACE => $this->client->getLevelFilterTrace(),
            LogLevel::DEBUG => $this->client->getLevelFilterDebug(),
            LogLevel::INFO => $this->client->getLevelFilterInfo(),
            LogLevel::WARN => $this->client->getLevelFilterWarn(),
            LogLevel::ERROR => $this->client->getLevelFilterError(),
            LogLevel::OFF => $this->client->getLevelFilterOff(),
            LogLevel::NONE => $this->client->getLevelFilterOff(),
        };
    }
}
