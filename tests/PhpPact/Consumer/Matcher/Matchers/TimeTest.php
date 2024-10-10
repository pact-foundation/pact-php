<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Time;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    #[TestWith([new Time('HH:mm:ss', null), '{"pact:matcher:type":"time","pact:generator:type":"Time","format":"HH:mm:ss","value":null}'])]
    #[TestWith([new Time('HH:mm:ss', '12:02::34'), '{"pact:matcher:type":"time","format":"HH:mm:ss","value":"12:02::34"}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = (new Time('HH:mm'))->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("DateTime matching expression doesn't support value of type %s", gettype(null)));
        json_encode($matcher);
    }

    #[TestWith([new Time("contains single quote '", '22:04'), "\"matching(time, 'contains single quote \\\'', '22:04')\""])]
    #[TestWith([new Time('HH:mm', "contains single quote '"), "\"matching(time, 'HH:mm', 'contains single quote \\\'')\""])]
    #[TestWith([new Time('HH:mm', '22:04'), "\"matching(time, 'HH:mm', '22:04')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $json): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($json, json_encode($matcher));
    }
}
