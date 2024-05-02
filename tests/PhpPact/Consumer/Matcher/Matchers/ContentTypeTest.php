<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
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

    public function testSerializeIntoExpression(): void
    {
        $matcher = new ContentType('text/csv');
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            '"matching(contentType, \'text\/csv\', \'text\/csv\')"',
            json_encode($matcher)
        );
    }
}
