<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\EqualityFormatter;
use PhpPact\Consumer\Matcher\Matchers\Equality;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EqualityFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new EqualityFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Equality(new \stdClass()), 'object'])]
    #[TestWith([new Equality(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Equality("contains single quote '"), "matching(equalTo, 'contains single quote \'')"])]
    #[TestWith([new Equality('example value'), "matching(equalTo, 'example value')"])]
    #[TestWith([new Equality(100.09), 'matching(equalTo, 100.09)'])]
    #[TestWith([new Equality(-99.99), 'matching(equalTo, -99.99)'])]
    #[TestWith([new Equality(100), 'matching(equalTo, 100)'])]
    #[TestWith([new Equality(-99), 'matching(equalTo, -99)'])]
    #[TestWith([new Equality(true), 'matching(equalTo, true)'])]
    #[TestWith([new Equality(false), 'matching(equalTo, false)'])]
    #[TestWith([new Equality(null), 'matching(equalTo, null)'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
