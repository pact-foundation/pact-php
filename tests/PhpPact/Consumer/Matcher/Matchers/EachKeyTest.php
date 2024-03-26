<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

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
        $eachKey = new EachKey($value, $rules);
        $this->assertJsonStringEqualsJsonString(
            '{"pact:matcher:type":"eachKey","value":{"abc":123,"def":111,"ghi":{"test":"value"}},"rules":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\w{3}"}]}',
            json_encode($eachKey)
        );
    }
}
