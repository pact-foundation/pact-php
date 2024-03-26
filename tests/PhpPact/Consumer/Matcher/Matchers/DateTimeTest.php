<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\DateTime;
use PhpPact\Consumer\Matcher\Matchers\GeneratorAwareMatcher;

class DateTimeTest extends GeneratorAwareMatcherTestCase
{
    protected function getMatcherWithoutExampleValue(): GeneratorAwareMatcher
    {
        return new DateTime();
    }

    protected function getMatcherWithExampleValue(): GeneratorAwareMatcher
    {
        return new DateTime("yyyy-MM-dd HH:mm", '2011-07-13 16:41');
    }

    /**
     * @testWith [null,                  "{\"pact:matcher:type\":\"datetime\",\"pact:generator:type\":\"DateTime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\"}"]
     *           ["1995-02-04T22:45:00", "{\"pact:matcher:type\":\"datetime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\",\"value\":\"1995-02-04T22:45:00\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $format = "yyyy-MM-dd'T'HH:mm:ss";
        $matcher = new DateTime($format, $value);
        $this->assertSame($json, json_encode($matcher));
    }
}
