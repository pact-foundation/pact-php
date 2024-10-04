<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Max;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{
    #[TestWith([2, '{"pact:matcher:type": "type", "value": [null], "max": 2}'])]
    #[TestWith([-2, '{"pact:matcher:type": "type", "value": [null], "max": 0}'])]
    public function testFormatJson(int $max, string $json): void
    {
        $matcher = new Max($max);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([2, '"atMost(2)"'])]
    #[TestWith([-22, '"atMost(0)"'])]
    public function testFormatExpression(int $max, string $expression): void
    {
        $matcher = new Max($max);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
