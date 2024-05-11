<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Json;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Json\ContentTypeFormatter;
use PhpPact\Consumer\Matcher\Matchers\ContentType;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ContentTypeFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ContentTypeFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new ContentType('plain/text'), '{"pact:matcher:type": "contentType", "value": "plain/text"}'])]
    #[TestWith([new ContentType('application/json', '{"key":"value"}'), '{"pact:matcher:type": "contentType", "value": "application/json"}'])]
    #[TestWith([new ContentType('application/xml', '<?xml?><test/>'), '{"pact:matcher:type": "contentType", "value": "application/xml"}'])]
    public function testFormat(MatcherInterface $matcher, string $json): void
    {
        $jsonEncoded = json_encode($this->formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString($json, $jsonEncoded);
    }
}
