<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\SemverFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PHPUnit\Framework\Attributes\TestWith;

class SemverTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Semver();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Semver('10.21.0-rc.1');
    }

    #[TestWith([null, '{"pact:matcher:type":"semver","pact:generator:type":"Regex","regex":"\\\\d+\\\\.\\\\d+\\\\.\\\\d+"}'])]
    #[TestWith(['1.2.3', '{"pact:matcher:type":"semver","value":"1.2.3"}'])]
    public function testSerialize(?string $value, string $json): void
    {
        $matcher = new Semver($value);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Semver();
        $this->assertInstanceOf(HasGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Semver();
        $this->assertInstanceOf(SemverFormatter::class, $matcher->createExpressionFormatter());
    }
}
