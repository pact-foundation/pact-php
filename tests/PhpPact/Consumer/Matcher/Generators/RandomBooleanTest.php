<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PHPUnit\Framework\TestCase;

class RandomBooleanTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new RandomBoolean();
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"RandomBoolean"}', $result);
    }
}
