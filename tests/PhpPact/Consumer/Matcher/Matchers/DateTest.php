<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Date;

class DateTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new Date();
    }

    /**
     * @testWith [null,         "{\"pact:matcher:type\":\"date\",\"pact:generator:type\":\"Date\",\"format\":\"yyyy-MM-dd\"}"]
     *           ["1995-02-04", "{\"pact:matcher:type\":\"date\",\"format\":\"yyyy-MM-dd\",\"value\":\"1995-02-04\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $format = 'yyyy-MM-dd';
        $this->matcher = new Date($format, $value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
