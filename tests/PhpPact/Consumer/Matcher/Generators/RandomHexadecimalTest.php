<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PHPUnit\Framework\TestCase;

class RandomHexadecimalTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new RandomHexadecimal(8);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"RandomHexadecimal","digits":8}', $result);
    }
}
