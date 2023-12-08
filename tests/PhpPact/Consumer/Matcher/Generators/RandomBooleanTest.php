<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomBoolean;
use PHPUnit\Framework\TestCase;

class RandomBooleanTest extends TestCase
{
    public function testSerialize(): void
    {
        $boolean = new RandomBoolean();
        $this->assertSame(
            '{"pact:generator:type":"RandomBoolean"}',
            json_encode($boolean)
        );
    }
}
