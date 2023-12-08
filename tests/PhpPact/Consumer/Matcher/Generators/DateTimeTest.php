<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    /**
     * @testWith [null,                    null,     "{\"pact:generator:type\":\"DateTime\"}"]
     *           ["yyyy-MM-dd'T'HH:mm:ss", null,     "{\"pact:generator:type\":\"DateTime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\"}"]
     *           [null,                    "+1 day", "{\"pact:generator:type\":\"DateTime\",\"expression\":\"+1 day\"}"]
     *           ["yyyy-MM-dd'T'HH:mm:ss", "+1 day", "{\"pact:generator:type\":\"DateTime\",\"format\":\"yyyy-MM-dd'T'HH:mm:ss\",\"expression\":\"+1 day\"}"]
     */
    public function testSerialize(?string $format, ?string $expression, string $json): void
    {
        $dateTime = new DateTime($format, $expression);
        $this->assertSame($json, json_encode($dateTime));
    }
}
