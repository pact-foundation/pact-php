<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Integer;

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

    /**
     * @testWith [null, "{\"pact:matcher:type\":\"integer\",\"pact:generator:type\":\"RandomInt\",\"min\":0,\"max\":10}"]
     *           [123,  "{\"pact:matcher:type\":\"integer\",\"value\":123}"]
     */
    public function testSerialize(?int $value, string $json): void
    {
        $matcher = new Integer($value);
        $this->assertSame($json, json_encode($matcher));
    }
}
