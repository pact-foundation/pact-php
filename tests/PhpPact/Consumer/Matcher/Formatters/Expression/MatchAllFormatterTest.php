<?php

namespace PhpPactTest\Consumer\Matcher\Formatters\Expression;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Formatters\Expression\MatchAllFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\StringValue;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\FormatterInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;

class MatchAllFormatterTest extends TestCase
{
    private FormatterInterface $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MatchAllFormatter();
    }

    public function testNotSupportedMatcher(): void
    {
        $matcher = new NullValue();
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Matcher %s is not supported by %s', $matcher->getType(), $this->formatter::class));
        $this->formatter->format($matcher);
    }

    #[TestWith([new MatchAll(['value'], [])])]
    public function testInvalidMatchers(MatchAll $matcher): void
    {
        $this->expectException(MatchingExpressionException::class);
        $this->expectExceptionMessage("Matcher 'matchAll' need at least 1 matchers");
        $this->formatter->format($matcher);
    }

    #[TestWith([new MatchAll(['abc' => 'xyz'], [new EachKey(["doesn't matter"], [new StringValue("contains single quote '")]), new EachValue(["doesn't matter"], [new StringValue("contains single quote '")])]), "eachKey(matching(type, 'contains single quote \'')), eachValue(matching(type, 'contains single quote \''))"])]
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new MinType(null, 2, false)]), 'atLeast(2)'])]
    #[TestWith([new MatchAll(['abc' => 1, 'def' => 234], [new MinType(null, 1, false), new MaxType(null, 2, false), new EachKey(["doesn't matter"], [new Regex('\w+', 'abc')]), new EachValue(["doesn't matter"], [new Type(100)])]), "atLeast(1), atMost(2), eachKey(matching(regex, '\w+', 'abc')), eachValue(matching(type, 100))"])]
    public function testFormat(MatcherInterface $matcher, string $expression): void
    {
        $result = $this->formatter->format($matcher);
        $this->assertIsString($result);
        $this->assertSame($expression, $result);
    }
}
