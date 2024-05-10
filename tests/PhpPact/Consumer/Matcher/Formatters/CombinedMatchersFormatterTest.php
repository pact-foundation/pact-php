<?php

namespace PhpPactTest\Consumer\Matcher\Formatters;

use PhpPact\Consumer\Matcher\Formatters\CombinedMatchersFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\Includes;
use PhpPact\Consumer\Matcher\Matchers\Integer;
use PhpPact\Consumer\Matcher\Matchers\MatchAll;
use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PhpPact\Consumer\Matcher\Matchers\MinType;
use PHPUnit\Framework\TestCase;

class CombinedMatchersFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $matcher = new MatchAll(['test 123' => 123], [
            new MinType([], 1),
            new MaxType([], 2),
            new EachKey([], [new Includes('test')]),
            new EachValue([], [new Integer(123)]),
        ]);
        $formatter = new CombinedMatchersFormatter();
        $jsonEncoded = json_encode($formatter->format($matcher));
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString('{"pact:matcher:type":[{"pact:matcher:type":"type","min":1,"value":[]},{"pact:matcher:type":"type","max":2,"value":[]},{"pact:matcher:type":"eachKey","rules":[{"pact:matcher:type":"include","value":"test"}],"value":[]},{"pact:matcher:type":"eachValue","rules":[{"pact:matcher:type":"integer","value":123}],"value":[]}],"value":{"test 123":123}}', $jsonEncoded);
    }
}
