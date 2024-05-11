<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ContentTypeFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\ContentTypeFormatter as JsonFormatter;
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

    public function testCreateJsonFormatter(): void
    {
        $matcher = new ContentType('text/plain');
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new ContentType('text/plain');
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
