<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new Regex('[\w\d]+');
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"Regex","regex":"[\\\\w\\\\d]+"}', $result);
    }
}
