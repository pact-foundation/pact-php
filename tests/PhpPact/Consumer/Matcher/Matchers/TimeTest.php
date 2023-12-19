<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;
use PhpPact\Consumer\Matcher\Matchers\Time;

class TimeTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new Time();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new Time('HH:mm', '21:15');
    }

    /**
     * @testWith [null,        "{\"pact:matcher:type\":\"time\",\"pact:generator:type\":\"Time\",\"format\":\"HH:mm:ss\"}"]
     *           ["12:02::34", "{\"pact:matcher:type\":\"time\",\"format\":\"HH:mm:ss\",\"value\":\"12:02::34\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $format = 'HH:mm:ss';
        $matcher = new Time($format, $value);
        $this->assertSame($json, json_encode($matcher));
    }
}
