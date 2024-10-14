<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MinTypeTest extends TestCase
{
    #[TestWith(['example text', 2, '{"pact:matcher:type": "type", "value": ["example text", "example text"], "min": 2}'])]
    #[TestWith(['example text', -2, '{"pact:matcher:type": "type", "value": ["example text"], "min": 0}'])]
    public function testFormatJson(mixed $value, int $min, string $json): void
    {
        $matcher = new MinType($value, $min);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith(["contains single quote '", 1, "\"atLeast(1), eachValue(matching(type, 'contains single quote \\\'')\""])]
    #[TestWith([null, 1, '"atLeast(1), eachValue(matching(type, null)"'])]
    #[TestWith(['example value', 1, "\"atLeast(1), eachValue(matching(type, 'example value')\""])]
    public function testFormatExpression(mixed $value, int $min, string $expression): void
    {
        $matcher = new MinType($value, $min);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
