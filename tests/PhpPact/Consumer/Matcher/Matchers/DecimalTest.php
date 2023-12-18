<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;

class DecimalTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Decimal();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Decimal(15.68);
    }

    /**
     * @testWith [null, "{\"pact:matcher:type\":\"decimal\",\"pact:generator:type\":\"RandomDecimal\",\"digits\":10}"]
     *           [1.23, "{\"pact:matcher:type\":\"decimal\",\"value\":1.23}"]
     */
    public function testSerialize(?float $value, string $json): void
    {
        $matcher = new Decimal($value);
        $this->assertSame($json, json_encode($matcher));
    }
}
