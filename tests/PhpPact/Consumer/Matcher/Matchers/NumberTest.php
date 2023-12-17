<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Number;

class NumberTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Number();
    }

    /**
     * @testWith [null, "{\"pact:matcher:type\":\"number\",\"pact:generator:type\":\"RandomInt\",\"min\":0,\"max\":10}"]
     *           [123,  "{\"pact:matcher:type\":\"number\",\"value\":123}"]
     *           [12.3, "{\"pact:matcher:type\":\"number\",\"value\":12.3}"]
     */
    public function testSerialize(int|float|null $value, string $json): void
    {
        $this->matcher = new Number($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
