<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EqualityTest extends TestCase
{
    #[TestWith([new Equality('exact this string'), new RandomString(), '{"pact:matcher:type":"equality","pact:generator:type":"RandomString","size": 10,"value":"exact this string"}'])]
    #[TestWith([new Equality('exact this string'), null, '{"pact:matcher:type":"equality","value":"exact this string"}'])]
    public function testFormatJson(Equality $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher = $matcher->withGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Equality(new \stdClass()), 'object'])]
    #[TestWith([new Equality(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new Equality("contains single quote '"), "\"matching(equalTo, 'contains single quote \\\'')\""])]
    #[TestWith([new Equality('example value'), "\"matching(equalTo, 'example value')\""])]
    #[TestWith([new Equality(100.09), '"matching(equalTo, 100.09)"'])]
    #[TestWith([new Equality(-99.99), '"matching(equalTo, -99.99)"'])]
    #[TestWith([new Equality(100), '"matching(equalTo, 100)"'])]
    #[TestWith([new Equality(-99), '"matching(equalTo, -99)"'])]
    #[TestWith([new Equality(true), '"matching(equalTo, true)"'])]
    #[TestWith([new Equality(false), '"matching(equalTo, false)"'])]
    #[TestWith([new Equality(null), '"matching(equalTo, null)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new Equality("contains single quote '"), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', \'contains single quote \\\\\'\'))"'])]
    #[TestWith([new Equality('example value'), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', \'example value\'))"'])]
    #[TestWith([new Equality(100.09), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', 100.09))"'])]
    #[TestWith([new Equality(-99.99), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', -99.99))"'])]
    #[TestWith([new Equality(100), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', 100))"'])]
    #[TestWith([new Equality(-99), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', -99))"'])]
    #[TestWith([new Equality(true), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', true))"'])]
    #[TestWith([new Equality(false), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', false))"'])]
    #[TestWith([new Equality(null), new ProviderState('${value}'), '"matching(equalTo, fromProviderState(\'${value}\', null))"'])]
    public function testFormatExpressionWithGenerator(Equality $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
