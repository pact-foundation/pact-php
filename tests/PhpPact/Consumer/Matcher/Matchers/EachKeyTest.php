<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\EachKeyFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
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

    public function testCreateJsonFormatter(): void
    {
        $matcher = new EachKey([], []);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new EachKey([], []);
        $this->assertInstanceOf(EachKeyFormatter::class, $matcher->createExpressionFormatter());
    }
}
