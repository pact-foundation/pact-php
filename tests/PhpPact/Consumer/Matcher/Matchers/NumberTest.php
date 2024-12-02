<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    #[TestWith([new Number(123), null, '{"pact:matcher:type":"number","value":123}'])]
    #[TestWith([new Number(12.3), null, '{"pact:matcher:type":"number","value":12.3}'])]
    #[TestWith([new Number(123), new RandomInt(), '{"pact:matcher:type":"number","pact:generator:type":"RandomInt","min":0,"max":10,"value":123}'])]
    #[TestWith([new Number(12.3), new RandomInt(), '{"pact:matcher:type":"number","pact:generator:type":"RandomInt","min":0,"max":10,"value":12.3}'])]
    public function testFormatJson(Number $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
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

    #[TestWith([new Number(-99), new ProviderState('${value}'), '"matching(number, fromProviderState(\'${value}\', -99))"'])]
    #[TestWith([new Number(100), new ProviderState('${value}'), '"matching(number, fromProviderState(\'${value}\', 100))"'])]
    #[TestWith([new Number(100.01), new ProviderState('${value}'), '"matching(number, fromProviderState(\'${value}\', 100.01))"'])]
    #[TestWith([new Number(-100.003), new ProviderState('${value}'), '"matching(number, fromProviderState(\'${value}\', -100.003))"'])]
    public function testFormatExpressionWithGenerator(Number $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
