<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class SemverTest extends TestCase
{
    #[TestWith([new Semver(null), '{"pact:matcher:type":"semver","pact:generator:type":"Regex","regex":"\\\\d+\\\\.\\\\d+\\\\.\\\\d+","value": null}'])]
    #[TestWith([new Semver('1.2.3'), '{"pact:matcher:type":"semver","value":"1.2.3"}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    public function testInvalidValue(): void
    {
        $matcher = new Semver();
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage("Semver matching expression doesn't support value of type NULL");
        json_encode($matcher);
    }

    #[TestWith([new Semver("contains single quote '"), "\"matching(semver, 'contains single quote \\\'')\""])]
    #[TestWith([new Semver('1.0.0'), "\"matching(semver, '1.0.0')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
