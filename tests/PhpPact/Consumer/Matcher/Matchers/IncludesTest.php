<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class IncludesTest extends TestCase
{
    public function testFormatJson(): void
    {
        $string = new Includes('contains this string');
        $this->assertSame(
            '{"pact:matcher:type":"include","value":"contains this string"}',
            json_encode($string)
        );
    }

    #[TestWith([new Includes("contains single quote '"), "\"matching(include, 'contains single quote \\\'')\""])]
    #[TestWith([new Includes('example value'), "\"matching(include, 'example value')\""])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
