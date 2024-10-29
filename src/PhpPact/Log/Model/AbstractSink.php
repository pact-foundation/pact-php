<?php

namespace PhpPact\Log\Model;

use PhpPact\Log\Enum\LogLevel;

abstract class AbstractSink implements SinkInterface
{
    public function __construct(private LogLevel $level)
    {
    }

    public function getLevel(): LogLevel
    {
        return $this->level;
    }
}
