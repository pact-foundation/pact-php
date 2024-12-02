<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\Date as DateGenerator;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    #[TestWith([new Date('yyyy-MM-dd', '1995-02-04'), null, '{"pact:matcher:type":"date","format":"yyyy-MM-dd","value":"1995-02-04"}'])]
    #[TestWith([new Date('yyyy-MM-dd', '1995-02-04'), new DateGenerator('yyyy-MM-dd'), '{"pact:generator:type":"Date","format":"yyyy-MM-dd","pact:matcher:type":"date","value":"1995-02-04"}'])]
    public function testFormatJson(Date $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
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
