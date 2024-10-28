<?php

namespace PhpPact\Log\Model;

use PhpPact\Log\Enum\LogLevel;

class File extends AbstractSink
{
    public function __construct(private string $path, LogLevel $level)
    {
        parent::__construct($level);
    }

    public function getSpecifier(): string
    {
        return "file {$this->path}";
    }
}
