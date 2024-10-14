<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    #[TestWith([new Date('yyyy-MM-dd', null), '{"pact:generator:type":"Date","format":"yyyy-MM-dd","pact:matcher:type":"date","value":null}'])]
    #[TestWith([new Date('yyyy-MM-dd', '1995-02-04'), '{"pact:matcher:type":"date","format":"yyyy-MM-dd","value":"1995-02-04"}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = (new Date('yyyy-MM-dd'))->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("DateTime matching expression doesn't support value of type %s", gettype(null)));
        json_encode($matcher);
    }

    #[TestWith([new Date("contains single quote '", '2012-04-12'), "\"matching(date, 'contains single quote \\\'', '2012-04-12')\""])]
    #[TestWith([new Date('yyyy-MM-dd', "contains single quote '"), "\"matching(date, 'yyyy-MM-dd', 'contains single quote \\\'')\""])]
    #[TestWith([new Date('yyyy-MM-dd', '2012-04-12'), "\"matching(date, 'yyyy-MM-dd', '2012-04-12')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new Date("contains single quote '", '2012-04-12'), new ProviderState('${value}'), '"matching(date, \'contains single quote \\\\\'\', fromProviderState(\'${value}\', \'2012-04-12\'))"'])]
    #[TestWith([new Date('yyyy-MM-dd', "contains single quote '"), new ProviderState('${value}'), '"matching(date, \'yyyy-MM-dd\', fromProviderState(\'${value}\', \'contains single quote \\\\\'\'))"'])]
    #[TestWith([new Date('yyyy-MM-dd', '2012-04-12'), new ProviderState('${value}'), '"matching(date, \'yyyy-MM-dd\', fromProviderState(\'${value}\', \'2012-04-12\'))"'])]
    public function testFormatExpressionWithGenerator(Date $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
