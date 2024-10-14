<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MaxTypeTest extends TestCase
{
    #[TestWith(['example text', 2, '{"pact:matcher:type": "type", "value": ["example text"], "max": 2}'])]
    #[TestWith(['example text', -2, '{"pact:matcher:type": "type", "value": ["example text"], "max": 0}'])]
    public function testFormatJson(mixed $value, int $max, string $json): void
    {
        $matcher = new MaxType($value, $max);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith(["contains single quote '", 2, "\"atMost(2), eachValue(matching(type, 'contains single quote \\\'')\""])]
    #[TestWith([null, 2, '"atMost(2), eachValue(matching(type, null)"'])]
    #[TestWith(['example value', 2, "\"atMost(2), eachValue(matching(type, 'example value')\""])]
    public function testFormatExpression(mixed $value, int $max, string $expression): void
    {
        $matcher = new MaxType($value, $max);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
