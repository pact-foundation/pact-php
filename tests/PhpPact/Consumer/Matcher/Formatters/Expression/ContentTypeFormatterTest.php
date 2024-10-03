<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ContentTypeFormatter;
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

    #[TestWith([new ContentType("contains single quote '", 'testing'), "matching(contentType, 'contains single quote \'', 'testing')"])]
    #[TestWith([new ContentType('plain/text', "contains single quote '"), "matching(contentType, 'plain/text', 'contains single quote \'')"])]
    #[TestWith([new ContentType('plain/text'), "matching(contentType, 'plain/text', '')"])]
    #[TestWith([new ContentType('application/json', '{"key":"value"}'), "matching(contentType, 'application/json', '{\"key\":\"value\"}')"])]
    #[TestWith([new ContentType('application/xml', '<?xml?><test/>'), "matching(contentType, 'application/xml', '<?xml?><test/>')"])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
