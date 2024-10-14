<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\ArrayContains;
use PhpPact\Consumer\Matcher\Matchers\Boolean;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ExpressionFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ExpressionFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new ArrayContains([1]);
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage('Matcher does not support expression format');
        $this->formatter->format($matcher);
    }

    #[TestWith([new NullValue(), '"matching(type, null)"'])]
    #[TestWith([new Boolean(false), '"matching(boolean, false)"'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertSame($expression, json_encode($result));
    }
}
