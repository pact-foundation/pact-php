<?php

namespace PhpPact\Config;

trait LogLevelTrait
{
    private ?string $logLevel = null;

    public function getLogLevel(): ?string
    {
        return $this->logLevel;
    }

    public function setLogLevel(string $logLevel): self
    {
        $logLevel = \strtoupper($logLevel);
        if (!\in_array($logLevel, ['TRACE', 'DEBUG', 'INFO', 'WARN', 'ERROR', 'OFF', 'NONE'])) {
            throw new \InvalidArgumentException('LogLevel ' . $logLevel . ' not supported.');
        }
        $this->logLevel = $logLevel;

        return $this;
    }
}
