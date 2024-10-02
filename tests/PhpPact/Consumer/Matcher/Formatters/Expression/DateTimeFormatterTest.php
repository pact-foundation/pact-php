<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\DateTimeFormatter;
use PhpPact\Consumer\Matcher\Matchers\Date;
use PhpPact\Consumer\Matcher\Matchers\DateTime;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Time;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class DateTimeFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DateTimeFormatter();
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
        $matcher = new Time('HH:mm');
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("DateTime formatter doesn't support value of type %s", gettype(null)));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Time("it's invalid format", 'testing'), "it's invalid format"])]
    #[TestWith([new Time('HH:mm', "it's invalid value"), "it's invalid value"])]
    public function testInvalidString(MatcherInterface $matcher, string $value): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('String value "%s" should not contains single quote', $value));
        $this->formatter->format($matcher);
    }

    #[TestWith([new DateTime('yyyy-MM-dd HH:mm:ssZZZZZ', '2020-05-21 16:44:32+10:00'), 'matching(datetime, \'yyyy-MM-dd HH:mm:ssZZZZZ\', \'2020-05-21 16:44:32+10:00\')'])]
    #[TestWith([new Date('yyyy-MM-dd', '2012-04-12'), 'matching(date, \'yyyy-MM-dd\', \'2012-04-12\')'])]
    #[TestWith([new Time('HH:mm', '22:04'), 'matching(time, \'HH:mm\', \'22:04\')'])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
