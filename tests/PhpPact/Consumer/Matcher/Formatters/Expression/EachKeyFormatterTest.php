<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Formatters\Expression\EachKeyFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;

class EachKeyFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new EachKeyFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new EachKey(['value'], [])])]
    #[TestWith([new EachKey(['value'], [new Type(1), new Type(2)])])]
    public function testInvalidRules(EachKey $matcher): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage(sprintf("Matcher 'eachKey' only support 1 rule in expression, %d provided", count($matcher->getRules())));
        $this->formatter->format($matcher);
    }

    #[TestWith([new EachKey(['value'], [new Integer(123)]), 'eachKey(matching(integer, 123))'])]
    #[TestWith([new EachKey(new stdClass(), [new Regex('\w+', 'example value')]), "eachKey(matching(regex, '\w+', 'example value'))"])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
