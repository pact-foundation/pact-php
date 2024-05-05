<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Formatters\Expression\EachValueFormatter;
use PhpPact\Consumer\Matcher\Formatters\Json\NoGeneratorFormatter;
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

    public function testCreateJsonFormatter(): void
    {
        $matcher = new EachValue([], []);
        $this->assertInstanceOf(NoGeneratorFormatter::class, $matcher->createJsonFormatter());
    }

    public function testCreateExpressionFormatter(): void
    {
        $matcher = new EachValue([], []);
        $this->assertInstanceOf(EachValueFormatter::class, $matcher->createExpressionFormatter());
    }
}
