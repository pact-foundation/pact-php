<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomHexadecimal;
use PHPUnit\Framework\TestCase;

class RandomHexadecimalTest extends TestCase
{
    public function testSerialize(): void
    {
        $hexaDecimal = new RandomHexadecimal(8);
        $this->assertSame(
            '{"digits":8,"pact:generator:type":"RandomHexadecimal"}',
            json_encode($hexaDecimal)
        );
    }
}
