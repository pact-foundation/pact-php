<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PHPUnit\Framework\TestCase;

class RandomIntTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new RandomInt(5, 15);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"RandomInt","min":5,"max":15}', $result);
    }
}
