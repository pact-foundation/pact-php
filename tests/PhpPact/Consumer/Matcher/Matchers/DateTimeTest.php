<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\DateTime as DateTimeGenerator;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Matchers\DateTime;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    #[TestWith([new DateTime("yyyy-MM-dd'T'HH:mm:ss", '1995-02-04T22:45:00'), null, '{"pact:matcher:type":"datetime","format":"yyyy-MM-dd\'T\'HH:mm:ss","value":"1995-02-04T22:45:00"}'])]
    #[TestWith([new DateTime("yyyy-MM-dd'T'HH:mm:ss", '1995-02-04T22:45:00'), new DateTimeGenerator("yyyy-MM-dd'T'HH:mm:ss"), '{"pact:matcher:type":"datetime","pact:generator:type":"DateTime","format":"yyyy-MM-dd\'T\'HH:mm:ss","value": "1995-02-04T22:45:00"}'])]
    public function testFormatJson(DateTime $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new DateTime("contains single quote '", '2020-05-21 16:44:32+10:00'), "\"matching(datetime, 'contains single quote \\\'', '2020-05-21 16:44:32+10:00')\""])]
    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', "contains single quote '"), "\"matching(datetime, 'yyyy-MM-dd HH:mm:ssZZZZZ', 'contains single quote \\\'')\""])]
    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00'), "\"matching(datetime, 'yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }

    #[TestWith([new DateTime("contains single quote '", '2020-05-21 16:44:32+10:00'), new ProviderState('${value}'), '"matching(datetime, \'contains single quote \\\\\'\', fromProviderState(\'${value}\', \'2020-05-21 16:44:32+10:00\'))"'])]
    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', "contains single quote '"), new ProviderState('${value}'), '"matching(datetime, \'yyyy-MM-dd HH:mm:ssZZZZZ\', fromProviderState(\'${value}\', \'contains single quote \\\\\'\'))"'])]
    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00'), new ProviderState('${value}'), '"matching(datetime, \'yyyy-MM-dd HH:mm:ssZZZZZ\', fromProviderState(\'${value}\', \'2020-05-21 16:44:32+10:00\'))"'])]
    public function testFormatExpressionWithGenerator(DateTime $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
