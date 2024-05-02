<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachValue;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class EachValueTest extends TestCase
{
    public function testSerialize(): void
    {
        $value = [
            'ab1',
            'cd2',
            'ef9',
        ];
        $rules = [
            new Type('string'),
            new Regex('\w{2}\d'),
        ];
        $matcher = new EachValue($value, $rules);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            '{"pact:matcher:type":"eachValue","value":["ab1","cd2","ef9"],"rules":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\w{2}\\\\d"}]}',
            $jsonEncoded
        );
    }

    public function testSerializeIntoExpression(): void
    {
        $matcher = new EachValue(['ab1', 'cd2'], [new Regex('\w{2}\d', 'xz3')]);
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            "\"eachValue(matching(regex, '\\\\w{2}\\\\d', 'xz3'))\"",
            json_encode($matcher)
        );
    }
}
