<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Boolean;

class BooleanTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Boolean();
    }

    /**
     * @testWith [null,  "{\"pact:matcher:type\":\"boolean\",\"pact:generator:type\":\"RandomBoolean\"}"]
     *           [true,  "{\"pact:matcher:type\":\"boolean\",\"value\":true}"]
     *           [false, "{\"pact:matcher:type\":\"boolean\",\"value\":false}"]
     */
    public function testSerialize(?bool $value, string $json): void
    {
        $this->matcher = new Boolean($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
