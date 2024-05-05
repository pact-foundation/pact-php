<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NullValueFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NullValueFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PHPUnit\Framework\TestCase;

class NullValueTest extends TestCase
{
    public function testSerialize(): void
    {
        $null = new NullValue();
        $this->assertSame(
            '{"pact:matcher:type":"null"}',
            json_encode($null)
        );
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new NullValue();
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new NullValue();
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
