<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\DateTime;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    #[TestWith([null, null, '{"pact:generator:type":"DateTime"}'])]
    #[TestWith(["yyyy-MM-dd'T'HH:mm:ss", null, '{"pact:generator:type":"DateTime","format":"yyyy-MM-dd\'T\'HH:mm:ss"}'])]
    #[TestWith([null, '+1 day', '{"pact:generator:type":"DateTime","expression":"+1 day"}'])]
    #[TestWith(["yyyy-MM-dd'T'HH:mm:ss", '+1 day', '{"pact:generator:type":"DateTime","format":"yyyy-MM-dd\'T\'HH:mm:ss","expression":"+1 day"}'])]
    public function testFormatJson(?string $format, ?string $expression, string $json): void
    {
        $generator = new DateTime($format, $expression);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame($json, $result);
    }
}
