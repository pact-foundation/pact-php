<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $contentType = new ContentType('text/csv');
        $this->assertSame(
            '{"value":"text\/csv","pact:matcher:type":"contentType"}',
            json_encode($contentType)
        );
    }
}
