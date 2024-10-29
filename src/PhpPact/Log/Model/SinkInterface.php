<?php

namespace PhpPact\Log\Model;

use PhpPact\Log\Enum\LogLevel;

interface SinkInterface
{
    public function getLevel(): LogLevel;

    public function getSpecifier(): string;
}
