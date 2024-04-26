<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Formatters\MinimalFormatter;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PHPUnit\Framework\TestCase;

class MinimalFormatterTest extends TestCase
{
    /**
     * @testWith [true,  "2001-01-02"]
     *           [false, "2002-02-03"]
     *           [true,  null]
     *           [false, null]
     */
    public function testFormat(bool $hasGenerator, ?string $value): void
    {
        $matcher = new Date('yyyy-MM-dd', $value);
        $formatter = new MinimalFormatter();
        $this->assertSame([
            'pact:matcher:type' => 'date',
            'format' => 'yyyy-MM-dd',
        ], $formatter->format($matcher));
    }
}
