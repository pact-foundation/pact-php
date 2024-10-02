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

    #[TestWith([new Regex("contains single quote '", 'value'), 'matching(regex, \'contains single quote \\\'\', \'value\')'])]
    #[TestWith([new Regex('\w \d', "contains single quote '"), 'matching(regex, \'\w \d\', \'contains single quote \\\'\')'])]
    #[TestWith([new Regex('\w{3}\d+', 'abc123'), 'matching(regex, \'\w{3}\d+\', \'abc123\')'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
