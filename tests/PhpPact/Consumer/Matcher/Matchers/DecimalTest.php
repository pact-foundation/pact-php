<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    #[TestWith([new Decimal(null), '{"pact:matcher:type":"decimal","pact:generator:type":"RandomDecimal","digits":10,"value":null}'])]
    #[TestWith([new Decimal(1.23), '{"pact:matcher:type":"decimal","value":1.23}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = (new Decimal())->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Decimal matching expression doesn't support value of type %s", gettype(null)));
        json_encode($matcher);
    }

    #[TestWith([new Decimal(-99), '"matching(decimal, -99)"'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100), '"matching(decimal, 100)"'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100.01), '"matching(decimal, 100.01)"'])]
    #[TestWith([new Decimal(-100.003), '"matching(decimal, -100.003)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new Decimal(-99), new ProviderState('${value}'), '"matching(decimal, fromProviderState(\'${value}\', -99))"'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100), new ProviderState('${value}'), '"matching(decimal, fromProviderState(\'${value}\', 100))"'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100.01), new ProviderState('${value}'), '"matching(decimal, fromProviderState(\'${value}\', 100.01))"'])]
    #[TestWith([new Decimal(-100.003), new ProviderState('${value}'), '"matching(decimal, fromProviderState(\'${value}\', -100.003))"'])]
    public function testFormatExpressionWithGenerator(Decimal $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
