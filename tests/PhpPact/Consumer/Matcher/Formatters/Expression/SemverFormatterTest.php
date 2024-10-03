<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\SemverFormatter;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Semver;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class SemverFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new SemverFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Semver(), 'NULL'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Semver formatter doesn't support value of type %s", $type));
        $this->formatter->format($matcher);
    }

    #[TestWith([new Semver("contains single quote '"), "matching(semver, 'contains single quote \'')"])]
    #[TestWith([new Semver('1.0.0'), "matching(semver, '1.0.0')"])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
