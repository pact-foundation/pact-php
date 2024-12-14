<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex as RegexGenerator;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    #[TestWith([new Regex('\d+', '12+'), null, '{"pact:matcher:type":"regex","regex":"\\\\d+","value":"12+"}'])]
    #[TestWith([new Regex('\d+', ['12.3', '456']), null, '{"pact:matcher:type":"regex","regex":"\\\\d+","value":["12.3","456"]}'])]
    #[TestWith([new Regex('\d+', '12+'), new RegexGenerator('\d+'), '{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\d+","value":"12+"}'])]
    #[TestWith([new Regex('\d+', ['12.3', '456']), new RegexGenerator('\d+'), '{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\d+","value":["12.3","456"]}'])]
    public function testFormatJson(Regex $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Regex('\w \d', ['key' => 'a 1']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Regex matching expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new Regex("['\w]+", "contains single quote '"), "\"matching(regex, '[\\\\'\\\\w]+', 'contains single quote \\\\'')\""])]
    #[TestWith([new Regex('\w{3}\d+', 'abc123'), "\"matching(regex, '\\\w{3}\\\d+', 'abc123')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
