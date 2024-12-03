<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PhpPact\Consumer\Matcher\Generators\Time as TimeGenerator;
use PhpPact\Consumer\Matcher\Matchers\Time;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    #[TestWith([new Time('HH:mm:ss', '12:02::34'), null, '{"pact:matcher:type":"time","format":"HH:mm:ss","value":"12:02::34"}'])]
    #[TestWith([new Time('HH:mm:ss', '12:02::34'), new TimeGenerator('HH:mm:ss'), '{"pact:matcher:type":"time","pact:generator:type":"Time","format":"HH:mm:ss","value":"12:02::34"}'])]
    public function testFormatJson(Time $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Time("contains single quote '", '22:04'), "\"matching(time, 'contains single quote \\\'', '22:04')\""])]
    #[TestWith([new Time('HH:mm', "contains single quote '"), "\"matching(time, 'HH:mm', 'contains single quote \\\'')\""])]
    #[TestWith([new Time('HH:mm', '22:04'), "\"matching(time, 'HH:mm', '22:04')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $json): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($json, json_encode($matcher));
    }

    #[TestWith([new Time("contains single quote '", '22:04'), new ProviderState('${value}'), '"matching(time, \'contains single quote \\\\\'\', fromProviderState(\'${value}\', \'22:04\'))"'])]
    #[TestWith([new Time('HH:mm', "contains single quote '"), new ProviderState('${value}'), '"matching(time, \'HH:mm\', fromProviderState(\'${value}\', \'contains single quote \\\\\'\'))"'])]
    #[TestWith([new Time('HH:mm', '22:04'), new ProviderState('${value}'), '"matching(time, \'HH:mm\', fromProviderState(\'${value}\', \'22:04\'))"'])]
    public function testFormatExpressionWithGenerator(Time $matcher, GeneratorInterface $generator, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $matcher = $matcher->withGenerator($generator);
        $this->assertSame($expression, json_encode($matcher));
    }
}
