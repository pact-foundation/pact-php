<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testSerialize(): void
    {
        $regex = new Regex('[\w\d]+');
        $this->assertSame(
            '{"regex":"[\\\\w\\\\d]+","pact:generator:type":"Regex"}',
            json_encode($regex)
        );
    }
}
