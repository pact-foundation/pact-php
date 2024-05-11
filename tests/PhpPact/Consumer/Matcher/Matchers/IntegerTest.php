<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\IntegerFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PHPUnit\Framework\Attributes\TestWith;

class IntegerTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Integer();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Integer(189);
    }

    #[TestWith([null, '{"pact:matcher:type":"integer","pact:generator:type":"RandomInt","min":0,"max":10}'])]
    #[TestWith([123, '{"pact:matcher:type":"integer","value":123}'])]
    public function testSerialize(?int $value, string $json): void
    {
        $matcher = new Integer($value);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Integer(123);
        $this->assertInstanceOf(HasGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Integer(123);
        $this->assertInstanceOf(IntegerFormatter::class, $matcher->createExpressionFormatter());
    }
}
