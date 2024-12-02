<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Generators\Regex;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Model\GeneratorInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class SemverTest extends TestCase
{
    #[TestWith([new Semver('1.2.3'), null, '{"pact:matcher:type":"semver","value":"1.2.3"}'])]
    #[TestWith([new Semver('1.2.3'), new Regex('\d+\.\d+\.\d+'), '{"pact:matcher:type":"semver","pact:generator:type":"Regex","regex":"\\\\d+\\\\.\\\\d+\\\\.\\\\d+","value": "1.2.3"}'])]
    public function testFormatJson(Semver $matcher, ?GeneratorInterface $generator, string $json): void
    {
        $matcher->setGenerator($generator);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new Semver("contains single quote '"), "\"matching(semver, 'contains single quote \\\'')\""])]
    #[TestWith([new Semver('1.0.0'), "\"matching(semver, '1.0.0')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
