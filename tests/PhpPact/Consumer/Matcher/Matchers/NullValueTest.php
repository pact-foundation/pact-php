<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\NullValue;
use PHPUnit\Framework\TestCase;

class NullValueTest extends TestCase
{
    public function testSerialize(): void
    {
        $null = new NullValue();
        $this->assertSame(
            '{"pact:matcher:type":"null"}',
            json_encode($null)
        );
    }
}
