<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\NumberFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Number;
use PHPUnit\Framework\Attributes\TestWith;

class NumberTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Number();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Number(56.73);
    }

    #[TestWith([null, '{"pact:matcher:type":"number","pact:generator:type":"RandomInt","min":0,"max":10}'])]
    #[TestWith([123, '{"pact:matcher:type":"number","value":123}'])]
    #[TestWith([12.3, '{"pact:matcher:type":"number","value":12.3}'])]
    public function testSerialize(int|float|null $value, string $json): void
    {
        $matcher = new Number($value);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new Number(123);
        $this->assertInstanceOf(HasGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new Number(123);
        $this->assertInstanceOf(NumberFormatter::class, $matcher->createExpressionFormatter());
    }
}
