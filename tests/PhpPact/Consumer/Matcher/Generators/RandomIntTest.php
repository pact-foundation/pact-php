<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PHPUnit\Framework\TestCase;

class RandomIntTest extends TestCase
{
    public function testSerialize(): void
    {
        $int = new RandomInt(5, 15);
        $this->assertSame(
            '{"min":5,"max":15,"pact:generator:type":"RandomInt"}',
            json_encode($int)
        );
    }
}
