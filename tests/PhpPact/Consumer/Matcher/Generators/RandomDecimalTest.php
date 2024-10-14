<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PHPUnit\Framework\TestCase;

class RandomDecimalTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new RandomDecimal(12);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"RandomDecimal","digits":12}', $result);
    }
}
