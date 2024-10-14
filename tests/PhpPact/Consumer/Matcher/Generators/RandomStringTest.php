<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomString;
use PHPUnit\Framework\TestCase;

class RandomStringTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new RandomString(11);
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"RandomString","size":11}', $result);
    }
}
