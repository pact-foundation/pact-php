<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Time;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    #[TestWith([null, null, '{"pact:generator:type":"Time"}'])]
    #[TestWith(['HH:mm:ss', null, '{"pact:generator:type":"Time","format":"HH:mm:ss"}'])]
    #[TestWith([null, '+1 hour', '{"pact:generator:type":"Time","expression":"+1 hour"}'])]
    #[TestWith(['HH:mm:ss', '+1 hour', '{"pact:generator:type":"Time","format":"HH:mm:ss","expression":"+1 hour"}'])]
    public function testFormatJson(?string $format, ?string $expression, string $json): void
    {
        $generator = new Time($format, $expression);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame($json, $result);
    }
}
