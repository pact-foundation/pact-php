<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\PluginFormatter;
use PhpPact\Consumer\Matcher\Matchers\EachKey;
use PhpPact\Consumer\Matcher\Matchers\Regex;
use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class EachKeyTest extends TestCase
{
    public function testSerialize(): void
    {
        $value = [
            'abc' => 123,
            'def' => 111,
            'ghi' => [
                'test' => 'value',
            ],
        ];
        $rules = [
            new Type('string'),
            new Regex('\w{3}'),
        ];
        $matcher = new EachKey($value, $rules);
        $jsonEncoded = json_encode($matcher);
        $this->assertIsString($jsonEncoded);
        $this->assertJsonStringEqualsJsonString(
            '{"pact:matcher:type":"eachKey","value":{"abc":123,"def":111,"ghi":{"test":"value"}},"rules":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\w{3}"}]}',
            $jsonEncoded
        );
    }

    public function testSerializeIntoExpression(): void
    {
        $matcher = new EachKey(['abc' => 123], [new Regex('\w{3}', 'abc')]);
        $matcher->setFormatter(new PluginFormatter());
        $this->assertSame(
            "\"eachKey(matching(regex, '\\\\w{3}', 'abc'))\"",
            json_encode($matcher)
        );
    }
}
