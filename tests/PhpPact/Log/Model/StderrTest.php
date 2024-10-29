<?php

namespace PhpPactTest\Log\Model;

use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Model\Stderr;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StderrTest extends TestCase
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
        $sink = new Stderr($logLevel);
        $this->assertSame('stderr', $sink->getSpecifier());
        $this->assertSame($logLevel, $sink->getLevel());
    }
}
