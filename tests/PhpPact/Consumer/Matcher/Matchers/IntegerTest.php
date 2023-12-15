<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Integer;

class IntegerTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Integer();
    }

    /**
     * @testWith [null, "{\"pact:matcher:type\":\"integer\",\"pact:generator:type\":\"RandomInt\",\"min\":0,\"max\":10}"]
     *           [123,  "{\"pact:matcher:type\":\"integer\",\"value\":123}"]
     */
    public function testSerialize(?int $value, string $json): void
    {
        $this->matcher = new Integer($value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
