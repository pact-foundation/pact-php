<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomDecimal;
use PHPUnit\Framework\TestCase;

class RandomDecimalTest extends TestCase
{
    public function testSerialize(): void
    {
        $decimal = new RandomDecimal(12);
        $this->assertSame(
            '{"digits":12,"pact:generator:type":"RandomDecimal"}',
            json_encode($decimal)
        );
    }
}
