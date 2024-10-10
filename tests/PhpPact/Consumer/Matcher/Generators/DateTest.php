<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Date;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    #[TestWith([null, null, '{"pact:generator:type":"Date"}'])]
    #[TestWith(['yyyy-MM-dd', null, '{"pact:generator:type":"Date","format":"yyyy-MM-dd"}'])]
    #[TestWith([null, '+1 day', '{"pact:generator:type":"Date","expression":"+1 day"}'])]
    #[TestWith(['yyyy-MM-dd', '+1 day', '{"pact:generator:type":"Date","format":"yyyy-MM-dd","expression":"+1 day"}'])]
    public function testFormatJson(?string $format, ?string $expression, string $json): void
    {
        $generator = new Date($format, $expression);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame($json, $result);
    }
}
