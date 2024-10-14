<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    #[TestWith([new Number(null), '{"pact:matcher:type":"number","pact:generator:type":"RandomInt","min":0,"max":10,"value":null}'])]
    #[TestWith([new Number(123), '{"pact:matcher:type":"number","value":123}'])]
    #[TestWith([new Number(12.3), '{"pact:matcher:type":"number","value":12.3}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = new Number();
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Number matching expression doesn't support value of type %s", gettype(null)));
        json_encode($matcher);
    }

    #[TestWith([new Number(-99), '"matching(number, -99)"'])]
    #[TestWith([new Number(100), '"matching(number, 100)"'])]
    #[TestWith([new Number(100.01), '"matching(number, 100.01)"'])]
    #[TestWith([new Number(-100.003), '"matching(number, -100.003)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
