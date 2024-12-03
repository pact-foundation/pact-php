<?php

namespace PhpPactTest\Consumer\Matcher\Enum;

use PhpPact\Consumer\Matcher\Enum\HttpStatus;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class HttpStatusTest extends TestCase
{
    #[TestWith([HttpStatus::INFORMATION, 100, 199])]
    public function testRange(HttpStatus $status, int $min, int $max): void
    {
        $range = $status->range();
        $this->assertSame($min, $range->min);
        $this->assertSame($max, $range->max);
    }
}
