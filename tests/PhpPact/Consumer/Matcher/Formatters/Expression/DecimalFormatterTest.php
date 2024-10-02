<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\DecimalFormatter;
use PhpPact\Consumer\Matcher\Matchers\Decimal;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DecimalFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DecimalFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    public function testInvalidValue(): void
    {
        $matcher = new Decimal();
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Decimal formatter doesn't support value of type %s", gettype(null)));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Decimal(-99), 'matching(decimal, -99)'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100), 'matching(decimal, 100)'])] // Provider verification will fail on this case
    #[TestWith([new Decimal(100.01), 'matching(decimal, 100.01)'])]
    #[TestWith([new Decimal(-100.003), 'matching(decimal, -100.003)'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
