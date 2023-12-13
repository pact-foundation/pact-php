<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Equality;
use PHPUnit\Framework\TestCase;

class EqualityTest extends TestCase
{
    public function testSerialize(): void
    {
        $string = new Equality('exact this string');
        $this->assertSame(
            '{"pact:matcher:type":"equality","value":"exact this string"}',
            json_encode($string)
        );
    }
}
