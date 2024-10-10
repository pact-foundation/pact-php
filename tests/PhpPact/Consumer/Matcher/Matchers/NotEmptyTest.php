<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomString;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    #[TestWith([new NotEmpty(['some text']), new RandomString(), '{"pact:matcher:type":"notEmpty","pact:generator:type":"RandomString","size": 10,"value":["some text"]}'])]
    #[TestWith([new NotEmpty(['some text']), null, '{"pact:matcher:type":"notEmpty","value":["some text"]}'])]
    public function testFormatJson(NotEmpty $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher = $matcher->withGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new NotEmpty(new \stdClass()), 'object'])]
    #[TestWith([new NotEmpty(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new NotEmpty("contains single quote '"), "\"notEmpty('contains single quote \\\'')\""])]
    #[TestWith([new NotEmpty('example value'), "\"notEmpty('example value')\""])]
    #[TestWith([new NotEmpty(100.09), '"notEmpty(100.09)"'])]
    #[TestWith([new NotEmpty(100), '"notEmpty(100)"'])]
    #[TestWith([new NotEmpty(true), '"notEmpty(true)"'])]
    #[TestWith([new NotEmpty(false), '"notEmpty(false)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
