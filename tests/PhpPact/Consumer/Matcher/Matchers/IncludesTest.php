<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Includes;
use PHPUnit\Framework\TestCase;

class IncludesTest extends TestCase
{
    public function testSerialize(): void
    {
        $string = new Includes('contains this string');
        $this->assertSame(
            '{"pact:matcher:type":"include","value":"contains this string"}',
            json_encode($string)
        );
    }
}
