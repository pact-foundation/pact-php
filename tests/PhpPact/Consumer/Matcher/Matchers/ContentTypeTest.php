<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    #[TestWith([new ContentType('plain/text'), '{"pact:matcher:type": "contentType", "value": "plain/text"}'])]
    #[TestWith([new ContentType('application/json', '{"key":"value"}'), '{"pact:matcher:type": "contentType", "value": "application/json"}'])]
    #[TestWith([new ContentType('application/xml', '<?xml?><test/>'), '{"pact:matcher:type": "contentType", "value": "application/xml"}'])]
    public function testFormatJson(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }

    #[TestWith([new ContentType("contains single quote '", 'testing'), "\"matching(contentType, 'contains single quote \\\'', 'testing')\""])]
    #[TestWith([new ContentType('plain/text', "contains single quote '"), "\"matching(contentType, 'plain\\/text', 'contains single quote \\\'')\""])]
    #[TestWith([new ContentType('plain/text'), "\"matching(contentType, 'plain\\/text', '')\""])]
    #[TestWith([new ContentType('application/json', '{"key":"value"}'), "\"matching(contentType, 'application\\/json', '{\\\"key\\\":\\\"value\\\"}')\""])]
    #[TestWith([new ContentType('application/xml', '<?xml?><test/>'), "\"matching(contentType, 'application\\/xml', '<?xml?><test\\/>')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
