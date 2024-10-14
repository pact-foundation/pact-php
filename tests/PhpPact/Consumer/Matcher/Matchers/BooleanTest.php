<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    #[TestWith([new Boolean(null), '{"pact:generator:type":"RandomBoolean","pact:matcher:type":"boolean","value":null}'])]
    #[TestWith([new Boolean(true), '{"pact:matcher:type":"boolean","value":true}'])]
    #[TestWith([new Boolean(false), '{"pact:matcher:type":"boolean","value":false}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = (new Boolean())->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Boolean matching expression doesn't support value of type %s", gettype(null)));
        json_encode($matcher);
    }

    #[TestWith([new Boolean(true), '"matching(boolean, true)"'])]
    #[TestWith([new Boolean(false), '"matching(boolean, false)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
