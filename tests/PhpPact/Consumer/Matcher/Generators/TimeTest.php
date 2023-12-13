<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    /**
     * @testWith [null,       null,      "{\"pact:generator:type\":\"Time\"}"]
     *           ["HH:mm:ss", null,      "{\"pact:generator:type\":\"Time\",\"format\":\"HH:mm:ss\"}"]
     *           [null,       "+1 hour", "{\"pact:generator:type\":\"Time\",\"expression\":\"+1 hour\"}"]
     *           ["HH:mm:ss", "+1 hour", "{\"pact:generator:type\":\"Time\",\"format\":\"HH:mm:ss\",\"expression\":\"+1 hour\"}"]
     */
    public function testSerialize(?string $format, ?string $expression, string $json): void
    {
        $time = new Time($format, $expression);
        $this->assertSame($json, json_encode($time));
    }
}
