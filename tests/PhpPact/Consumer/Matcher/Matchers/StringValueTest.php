<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    #[TestWith([null, false, '{"pact:matcher:type": "type", "pact:generator:type": "RandomString", "size": 10, "value": "some string"}'])]
    #[TestWith([null, true, '{"pact:matcher:type": "type", "pact:generator:type": "Regex", "regex": "\\\\w{3}", "value": "some string"}'])]
    #[TestWith(['example text', false, '{"pact:matcher:type": "type", "value": "example text"}'])]
    public function testFormatJson(?string $value, bool $hasGenerator, string $json): void
    {
        $matcher = new StringValue($value);
        if ($hasGenerator) {
            $matcher->setGenerator(new Regex('\w{3}'));
        }
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith(["contains single quote '", "\"matching(type, 'contains single quote \\\'')\""])]
    #[TestWith(['value', "\"matching(type, 'value')\""])]
    #[TestWith([null, "\"matching(type, 'some string')\""])]
    public function testFormatExpression(?string $value, string $expression): void
    {
        $matcher = new StringValue($value);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
