<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\StringValueFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\StringValueFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    #[TestWith([null, '{"pact:matcher:type":"type","value":"some string","pact:generator:type":"RandomString","size":10}'])]
    #[TestWith(['test', '{"pact:matcher:type":"type","value":"test"}'])]
    public function testSerialize(?string $value, string $json): void
    {
        $matcher = new StringValue($value);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new StringValue('abc');
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new StringValue('abc');
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
