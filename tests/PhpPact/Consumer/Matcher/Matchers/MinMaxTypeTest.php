<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MinMaxTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MinMaxTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinMaxTypeTest extends TestCase
{
    #[TestWith([-2, 5, '{"pact:matcher:type":"type","min":0,"max":5,"value":[1.23]}'])]
    #[TestWith([-2, -5, '{"pact:matcher:type":"type","min":0,"max":0,"value":[1.23]}'])]
    #[TestWith([2, 5, '{"pact:matcher:type":"type","min":2,"max":5,"value":[1.23,1.23]}'])]
    #[TestWith([2, -5, '{"pact:matcher:type":"type","min":2,"max":0,"value":[1.23,1.23]}'])]
    public function testSerialize(int $min, int $max, string $json): void
    {
        $matcher = new MinMaxType(1.23, $min, $max);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MinMaxType(null, 0, 1);
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MinMaxType(null, 0, 1);
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
