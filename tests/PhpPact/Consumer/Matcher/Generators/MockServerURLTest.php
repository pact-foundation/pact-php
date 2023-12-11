<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\MockServerURL;
use PHPUnit\Framework\TestCase;

class MockServerURLTest extends TestCase
{
    public function testSerialize(): void
    {
        $url = new MockServerURL('.*(/path)$', 'http://localhost:1234/path');
        $this->assertSame(
            '{"regex":".*(\/path)$","example":"http:\/\/localhost:1234\/path","pact:generator:type":"MockServerURL"}',
            json_encode($url)
        );
    }
}
