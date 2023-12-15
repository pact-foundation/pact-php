<?php

namespace PhpPactTest\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Matchers\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testSerialize(): void
    {
        $value = (object) ['key' => 'value'];
        $object = new Type($value);
        $this->assertSame(
            '{"pact:matcher:type":"type","value":{"key":"value"}}',
            json_encode($object)
        );
    }
}
