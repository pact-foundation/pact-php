<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinMaxTypeTest extends TestCase
{
    #[TestWith(['example text', 2, 3, '{"pact:matcher:type": "type", "value": ["example text", "example text"], "min": 2, "max": 3}'])]
    #[TestWith(['example text', -2, 3, '{"pact:matcher:type": "type", "value": ["example text"], "min": 0, "max": 3}'])]
    #[TestWith(['example text', 2, -3, '{"pact:matcher:type": "type", "value": ["example text", "example text"], "min": 2, "max": 0}'])]
    #[TestWith(['example text', -2, -3, '{"pact:matcher:type": "type", "value": ["example text"], "min": 0, "max": 0}'])]
    public function testFormatJson(mixed $value, int $min, int $max, string $json): void
    {
        $matcher = new MinMaxType($value, $min, $max);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith(["contains single quote '", 2, 3, "\"atLeast(2), atMost(3), eachValue(matching(type, 'contains single quote \\\'')\""])]
    #[TestWith([null, 2, 3, '"atLeast(2), atMost(3), eachValue(matching(type, null)"'])]
    #[TestWith(['example value', 2, 3, "\"atLeast(2), atMost(3), eachValue(matching(type, 'example value')\""])]
    public function testFormatExpression(mixed $value, int $min, int $max, string $expression): void
    {
        $matcher = new MinMaxType($value, $min, $max);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
