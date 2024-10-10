<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PHPUnit\Framework\TestCase;

class MockServerURLTest extends TestCase
{
    public function testFormatJson(): void
    {
        $generator = new MockServerURL('.*(/path)$', 'http://localhost:1234/path');
        $attributes = $generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"MockServerURL","regex":".*(\/path)$","example":"http:\/\/localhost:1234\/path"}', $result);
    }
}
