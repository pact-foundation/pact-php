<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\MinMaxType;
use PHPUnit\Framework\TestCase;

class MinMaxTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $values = [
            1.23,
            2.34,
        ];
        $array = new MinMaxType($values, 2, 5);
        $this->assertSame(
            '{"pact:matcher:type":"type","min":2,"max":5,"value":[1.23,2.34]}',
            json_encode($array)
        );
    }
}
