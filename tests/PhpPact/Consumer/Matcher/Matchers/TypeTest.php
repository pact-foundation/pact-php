<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testFormatJson(): void
    {
        $value = (object) ['key' => 'value'];
        $object = new Type($value);
        $this->assertSame(
            '{"pact:matcher:type":"type","value":{"key":"value"}}',
            json_encode($object)
        );
    }

    #[TestWith([new Type(new \stdClass()), 'object'])]
    #[TestWith([new Type(['key' => 'value']), 'array'])]
    public function testInvalidValue(MatcherInterface $matcher, string $type): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf("Expression doesn't support value of type %s", $type));
        json_encode($matcher);
    }

    #[TestWith([new Type("contains single quote '"), "\"matching(type, 'contains single quote \\\'')\""])]
    #[TestWith([new Type('example value'), "\"matching(type, 'example value')\""])]
    #[TestWith([new Type(100.09), '"matching(type, 100.09)"'])]
    #[TestWith([new Type(-99.99), '"matching(type, -99.99)"'])]
    #[TestWith([new Type(100), '"matching(type, 100)"'])]
    #[TestWith([new Type(-99), '"matching(type, -99)"'])]
    #[TestWith([new Type(true), '"matching(type, true)"'])]
    #[TestWith([new Type(false), '"matching(type, false)"'])]
    #[TestWith([new Type(null), '"matching(type, null)"'])]
    public function testFormatExpression(MatcherInterface $matcher, string $expression): void
    {
        $matcher = $matcher->withFormatter(new ExpressionFormatter());
        $this->assertSame($expression, json_encode($matcher));
    }
}
