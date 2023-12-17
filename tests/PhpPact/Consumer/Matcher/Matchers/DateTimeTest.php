<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\DateTime;

class DateTimeTest extends GeneratorAwareMatcherTestCase
{
    protected function setUp(): void
    {
        $this->matcher = new DateTime();
    }

    /**
     * @testWith [null,                  "{\"pact:matcher:type\":\"datetime\",\"pact:generator:type\":\"DateTime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\"}"]
     *           ["1995-02-04T22:45:00", "{\"pact:matcher:type\":\"datetime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\",\"value\":\"1995-02-04T22:45:00\"}"]
     */
    public function testSerialize(?string $value, string $json): void
    {
        $format = "yyyy-MM-dd'T'HH:mm:ss";
        $this->matcher = new DateTime($format, $value);
        $this->assertSame($json, json_encode($this->matcher));
    }
}
