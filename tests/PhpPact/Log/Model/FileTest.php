<?php

namespace PhpPactTest\Log\Model;

use PhpPact\Log\Enum\LogLevel;
use PhpPact\Log\Model\File;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
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
        $path = '/path/to/log.txt';
        $sink = new File($path, $logLevel);
        $this->assertSame("file {$path}", $sink->getSpecifier());
        $this->assertSame($logLevel, $sink->getLevel());
    }
}
