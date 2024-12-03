<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    #[TestWith(['example text', null, '{"pact:matcher:type": "type", "value": "example text"}'])]
    #[TestWith(['example text', new Regex('\w{3}'), '{"pact:matcher:type": "type", "pact:generator:type": "Regex", "regex": "\\\\w{3}", "value": "example text"}'])]
    public function testFormatJson(string $value, ?GeneratorInterface $generator, string $json): void
    {
        $matcher = new StringValue($value);
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith(["contains single quote '", "\"matching(type, 'contains single quote \\\'')\""])]
    #[TestWith(['value', "\"matching(type, 'value')\""])]
    public function testFormatExpression(string $value, string $expression): void
    {
        $matcher = new StringValue($value);
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
