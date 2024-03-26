<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\MinType;
use PHPUnit\Framework\TestCase;

class MinTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $values = [
            123,
            34,
            5,
        ];
        $array = new MinType($values, 3);
        $this->assertSame(
            '{"pact:matcher:type":"type","min":3,"value":[123,34,5]}',
            json_encode($array)
        );
    }
}
