<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MaxTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MaxTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MaxTypeTest extends TestCase
{
    #[TestWith([-3, '{"pact:matcher:type":"type","max":0,"value":["string value"]}'])]
    #[TestWith([3, '{"pact:matcher:type":"type","max":3,"value":["string value"]}'])]
    public function testSerialize(int $max, string $json): void
    {
        $matcher = new MaxType('string value', $max);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MaxType(null, 0);
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MaxType(null, 0);
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
