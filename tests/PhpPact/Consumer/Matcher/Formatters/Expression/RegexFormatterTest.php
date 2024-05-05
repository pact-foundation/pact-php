<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\RegexFormatter;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class RegexFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new RegexFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Regex('\w \d'), 'NULL'])]
    #[TestWith([new Regex('\w \d', ['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Regex formatter doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Regex("it's invalid regex", 'value'), "it's invalid regex"])]
    #[TestWith([new Regex('\w \d', "it's invalid value"), "it's invalid value"])]
    public function testInvalidString(MatcherInterface $matcher, string $value): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('String value "%s" should not contains single quote', $value));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Regex('\\w{3}\\d+', 'abc123'), '"matching(regex, \'\\\\w{3}\\\\d+\', \'abc123\')"'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $this->assertSame($expression, json_encode($this->formatter->format($matcher)));
    }
}
