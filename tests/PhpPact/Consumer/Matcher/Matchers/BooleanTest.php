<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    #[TestWith([new Boolean(true), null, '{"pact:matcher:type":"boolean","value":true}'])]
    #[TestWith([new Boolean(false), null, '{"pact:matcher:type":"boolean","value":false}'])]
    #[TestWith([new Boolean(true), new RandomBoolean(), '{"pact:generator:type":"RandomBoolean","pact:matcher:type":"boolean","value":true}'])]
    #[TestWith([new Boolean(false), new RandomBoolean(), '{"pact:generator:type":"RandomBoolean","pact:matcher:type":"boolean","value":false}'])]
    public function testFormatJson(Boolean $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Boolean(true), '"matching(boolean, true)"'])]
    #[TestWith([new Boolean(false), '"matching(boolean, false)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
