<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\BooleanFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\HasGeneratorFormatter;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PHPUnit\Framework\Attributes\TestWith;

class BooleanTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Boolean();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Boolean(false);
    }

    #[TestWith([null, '{"pact:matcher:type":"boolean","pact:generator:type":"RandomBoolean"}'])]
    #[TestWith([true, '{"pact:matcher:type":"boolean","value":true}'])]
    #[TestWith([false, '{"pact:matcher:type":"boolean","value":false}'])]
    public function testSerialize(?bool $value, string $json): void
    {
        $matcher = new Boolean($value);
        $this->assertSame($json, json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = $this->getMatcherWithoutExampleValue();
        $this->assertInstanceOf(HasGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = $this->getMatcherWithoutExampleValue();
        $this->assertInstanceOf(BooleanFormatter::class, $matcher->createExpressionFormatter());
    }
}
