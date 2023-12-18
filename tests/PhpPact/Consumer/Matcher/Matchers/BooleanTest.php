<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;

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

    /**
     * @testWith [null,  "{\"pact:matcher:type\":\"boolean\",\"pact:generator:type\":\"RandomBoolean\"}"]
     *           [true,  "{\"pact:matcher:type\":\"boolean\",\"value\":true}"]
     *           [false, "{\"pact:matcher:type\":\"boolean\",\"value\":false}"]
     */
    public function testSerialize(?bool $value, string $json): void
    {
        $matcher = new Boolean($value);
        $this->assertSame($json, json_encode($matcher));
    }
}
