<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @testWith [null,         null,     "{\"pact:generator:type\":\"Date\"}"]
     *           ["yyyy-MM-dd", null,     "{\"pact:generator:type\":\"Date\",\"format\":\"yyyy-MM-dd\"}"]
     *           [null,         "+1 day", "{\"pact:generator:type\":\"Date\",\"expression\":\"+1 day\"}"]
     *           ["yyyy-MM-dd", "+1 day", "{\"pact:generator:type\":\"Date\",\"format\":\"yyyy-MM-dd\",\"expression\":\"+1 day\"}"]
     */
    public function testSerialize(?string $format, ?string $expression, string $json): void
    {
        $date = new Date($format, $expression);
        $this->assertSame($json, json_encode($date));
    }
}
