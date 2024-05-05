<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\EqualityFormatter;
use PhpPact\Consumer\Matcher\Formatters\Expression\TypeFormatter;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TypeFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new TypeFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Type(new \stdClass()), 'object'])]
    #[TestWith([new Type(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Type("it's invalid value"), "it's invalid value"])]
    public function testInvalidString(MatcherInterface $matcher, string $value): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('String value "%s" should not contains single quote', $value));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Type('example value'), '"matching(type, \'example value\')"'])]
    #[TestWith([new Type(100.09), '"matching(type, 100.09)"'])]
    #[TestWith([new Type(-99.99), '"matching(type, -99.99)"'])]
    #[TestWith([new Type(100), '"matching(type, 100)"'])]
    #[TestWith([new Type(-99), '"matching(type, -99)"'])]
    #[TestWith([new Type(true), '"matching(type, true)"'])]
    #[TestWith([new Type(false), '"matching(type, false)"'])]
    #[TestWith([new Type(null), '"matching(type, null)"'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $this->assertSame($expression, json_encode($this->formatter->format($matcher)));
    }
}
