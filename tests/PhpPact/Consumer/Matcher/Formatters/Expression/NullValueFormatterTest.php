<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Formatters\Expression\NullValueFormatter;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NullValueFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NullValueFormatter();
    }

    #[TestWith([new NullValue(), 'matching(type, null)'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
