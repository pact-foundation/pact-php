<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $matcher = new ContentType('text/csv');
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            '{"value":"text\/csv","pact:matcher:type":"contentType"}',
            $jsonEncoded
        );
    }
}
