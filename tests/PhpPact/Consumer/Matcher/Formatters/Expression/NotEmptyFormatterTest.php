<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\NotEmptyFormatter;
use PhpPact\Consumer\Matcher\Matchers\NotEmpty;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class NotEmptyFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NotEmptyFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new NotEmpty(new \stdClass()), 'object'])]
    #[TestWith([new NotEmpty(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new NotEmpty("it's invalid value"), "it's invalid value"])]
    public function testInvalidString(MatcherInterface $matcher, string $value): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('String value "%s" should not contains single quote', $value));
        $this->formatter->format($matcher);
    }

    #[TestWith([new NotEmpty('example value'), 'notEmpty(\'example value\')'])]
    #[TestWith([new NotEmpty(100.09), 'notEmpty(100.09)'])]
    #[TestWith([new NotEmpty(100), 'notEmpty(100)'])]
    #[TestWith([new NotEmpty(true), 'notEmpty(true)'])]
    #[TestWith([new NotEmpty(false), 'notEmpty(false)'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
