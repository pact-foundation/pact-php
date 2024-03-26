<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\MaxType;
use PHPUnit\Framework\TestCase;

class MaxTypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $values = [
            'string value',
        ];
        $array = new MaxType($values, 3);
        $this->assertSame(
            '{"pact:matcher:type":"type","max":3,"value":["string value"]}',
            json_encode($array)
        );
    }
}
