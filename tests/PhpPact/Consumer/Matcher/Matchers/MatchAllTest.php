<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
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
        $matcher = new MatchAll(['key' => 123], [new MinType([], 1), new MaxType([], 2), new EachKey([], [new Type('test')]), new EachValue([], [new Type(123)])]);
        $this->assertSame('{"pact:matcher:type":[{"pact:matcher:type":"type","min":1,"value":[]},{"pact:matcher:type":"type","max":2,"value":[]},{"pact:matcher:type":"eachKey","rules":[{"pact:matcher:type":"type","value":"test"}],"value":[]},{"pact:matcher:type":"eachValue","rules":[{"pact:matcher:type":"type","value":123}],"value":[]}],"value":{"key":123}}', json_encode($matcher));
    }
}
