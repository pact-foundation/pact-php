<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

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
        $eachValue = new EachValue($value, $rules);
        $this->assertSame(
            '{"pact:matcher:type":"eachValue","value":["ab1","cd2","ef9"],"rules":[{"pact:matcher:type":"type","value":"string"},{"pact:matcher:type":"regex","pact:generator:type":"Regex","regex":"\\\\w{2}\\\\d"}]}',
            json_encode($eachValue)
        );
    }
}
