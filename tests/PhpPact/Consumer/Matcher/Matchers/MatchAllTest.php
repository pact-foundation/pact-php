<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\MatchAllFormatter as ExpressionFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\MatchAllFormatter as JsonFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class MatchAllTest extends TestCase
{
    public function testNestedCombinedMatchers(): void
    {
        $this->expectException(MatcherNotSupportedException::class);
        $this->expectExceptionMessage('Nested combined matchers are not supported');
        new MatchAll([], [new MatchAll([], [])]);
    }

    public function testSerialize(): void
    {
        $matcher = new MatchAll(['key' => 123], [new MinType(null, 1), new MaxType(null, 2), new EachKey([], [new Type('test')]), new EachValue([], [new Type(123)])]);
        $this->assertSame('{"pact:matcher:type":[{"pact:matcher:type":"type","min":1,"value":[null]},{"pact:matcher:type":"type","max":2,"value":[null]},{"pact:matcher:type":"eachKey","rules":[{"pact:matcher:type":"type","value":"test"}],"value":[]},{"pact:matcher:type":"eachValue","rules":[{"pact:matcher:type":"type","value":123}],"value":[]}],"value":{"key":123}}', json_encode($matcher));
    }

    public function testCreateJsonFormatter(): void
    {
        $matcher = new MatchAll([], []);
        $this->assertInstanceOf(JsonFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new MatchAll([], []);
        $this->assertInstanceOf(ExpressionFormatter::class, $matcher->createExpressionFormatter());
    }
}
