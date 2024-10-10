<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    #[TestWith([new RandomString(), '{"pact:matcher:type":"type","pact:generator:type":"RandomString","size": 10,"value":{"key":"value"}}'])]
    #[TestWith([null, '{"pact:matcher:type":"type","value":{"key":"value"}}'])]
    public function testFormatJson(?GeneratorInterface $generator, string $json): void
    {
        $value = (object) ['key' => 'value'];
        $matcher = new Type($value);
        $matcher = $matcher->withGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Type(new \stdClass()), 'object'])]
    #[TestWith([new Type(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new Type("contains single quote '"), "\"matching(type, 'contains single quote \\\'')\""])]
    #[TestWith([new Type('example value'), "\"matching(type, 'example value')\""])]
    #[TestWith([new Type(100.09), '"matching(type, 100.09)"'])]
    #[TestWith([new Type(-99.99), '"matching(type, -99.99)"'])]
    #[TestWith([new Type(100), '"matching(type, 100)"'])]
    #[TestWith([new Type(-99), '"matching(type, -99)"'])]
    #[TestWith([new Type(true), '"matching(type, true)"'])]
    #[TestWith([new Type(false), '"matching(type, false)"'])]
    #[TestWith([new Type(null), '"matching(type, null)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new Type("contains single quote '"), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', \'contains single quote \\\\\'\'))"'])]
    #[TestWith([new Type('example value'), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', \'example value\'))"'])]
    #[TestWith([new Type(100.09), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', 100.09))"'])]
    #[TestWith([new Type(-99.99), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', -99.99))"'])]
    #[TestWith([new Type(100), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', 100))"'])]
    #[TestWith([new Type(-99), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', -99))"'])]
    #[TestWith([new Type(true), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', true))"'])]
    #[TestWith([new Type(false), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', false))"'])]
    #[TestWith([new Type(null), new ProviderState('${value}'), '"matching(type, fromProviderState(\'${value}\', null))"'])]
    public function testFormatExpressionWithGenerator(Type $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
