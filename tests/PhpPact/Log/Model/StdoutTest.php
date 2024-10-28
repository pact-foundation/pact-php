<?php

namespace PhpPactTest\Log\Model;

use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Model\Stdout;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StdoutTest extends TestCase
{
    #[TestWith([LogLevel::TRACE])]
    #[TestWith([LogLevel::DEBUG])]
    #[TestWith([LogLevel::INFO])]
    #[TestWith([LogLevel::WARN])]
    #[TestWith([LogLevel::ERROR])]
    #[TestWith([LogLevel::OFF])]
    #[TestWith([LogLevel::NONE])]
    public function testConstructor(LogLevel $logLevel): void
    {
        $sink = new Stdout($logLevel);
        $this->assertSame('stdout', $sink->getSpecifier());
        $this->assertSame($logLevel, $sink->getLevel());
    }
}
