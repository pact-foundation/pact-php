<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    #[TestWith([new Integer(123), null, '{"pact:matcher:type":"integer","value":123}'])]
    #[TestWith([new Integer(123), new RandomInt(), '{"pact:matcher:type":"integer","pact:generator:type":"RandomInt","min":0,"max":10,"value": 123}'])]
    public function testFormatJson(Integer $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Integer(-99), '"matching(integer, -99)"'])]
    #[TestWith([new Integer(100), '"matching(integer, 100)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new Integer(-99), new ProviderState('${value}'), '"matching(integer, fromProviderState(\'${value}\', -99))"'])]
    #[TestWith([new Integer(100), new ProviderState('${value}'), '"matching(integer, fromProviderState(\'${value}\', 100))"'])]
    public function testFormatExpressionWithGenerator(Integer $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
