<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\RandomString;
use PHPUnit\Framework\TestCase;

class RandomStringTest extends TestCase
{
    public function testSerialize(): void
    {
        $string = new RandomString(11);
        $this->assertSame(
            '{"size":11,"pact:generator:type":"RandomString"}',
            json_encode($string)
        );
    }
}
