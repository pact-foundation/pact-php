<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Decimal;

class DecimalTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Decimal();
    }

    /**
     * @testWith [null, "{\"pact:matcher:type\":\"decimal\",\"pact:generator:type\":\"RandomDecimal\",\"digits\":10}"]
     *           [1.23, "{\"pact:matcher:type\":\"decimal\",\"value\":1.23}"]
     */
    public function testSerialize(?float $value, string $json): void
    {
        $this->matcher = new Decimal($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
