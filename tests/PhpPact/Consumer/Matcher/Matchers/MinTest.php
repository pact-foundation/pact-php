<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Min;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinTest extends TestCase
{
    #[TestWith([2, '{"pact:matcher:type": "type", "value": [null, null], "min": 2}'])]
    #[TestWith([-2, '{"pact:matcher:type": "type", "value": [null], "min": 0}'])]
    public function testFormatJson(int $min, string $json): void
    {
        $matcher = new Min($min);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([1, '"atLeast(1)"'])]
    #[TestWith([-1, '"atLeast(0)"'])]
    public function testFormatExpression(int $min, string $expression): void
    {
        $matcher = new Min($min);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
