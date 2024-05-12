<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\MinTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MinTypeFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinTypeTest extends TestCase
{
    #[TestWith([-3, '{"pact:matcher:type":"type","min":0,"value":[123]}'])]
    #[TestWith([3, '{"pact:matcher:type":"type","min":3,"value":[123,123,123]}'])]
    public function testSerialize(int $min, string $json): void
    {
        $matcher = new MinType(123, $min);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MinType(null, 0);
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MinType(null, 0);
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
