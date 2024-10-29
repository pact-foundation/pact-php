<?php

namespace PhpPact\Log\PHPUnit;

use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\LoggerInterface;
use PhpPact\Log\Model\File;
use PhpPact\Log\Model\Stdout;
use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;

final class PactLoggingSubscriber implements StartedSubscriber
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function notify(Started $event): void
    {
        $logFile = \getenv('PACT_LOG');
        $logLevel = \getenv('PACT_LOGLEVEL');
        if (is_string($logFile) && is_string($logLevel)) {
            $this->logger->attach(new File($logFile, LogLevel::from(\strtoupper($logLevel))));
        }
        if ($logFile === false && is_string($logLevel)) {
            $this->logger->attach(new Stdout(LogLevel::from(\strtoupper($logLevel))));
        }
        if (is_string($logFile) && $logLevel === false) {
            $this->logger->attach(new File($logFile, LogLevel::INFO));
        }
        if (is_string($logFile) || is_string($logLevel)) {
            $this->logger->apply();
        }
    }
}
