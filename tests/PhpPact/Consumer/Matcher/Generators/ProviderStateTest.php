<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PHPUnit\Framework\TestCase;

class ProviderStateTest extends TestCase
{
    private ProviderState $generator;

    protected function setUp(): void
    {
        $this->generator = new ProviderState('/products/${id}');
    }

    public function testFormatJson(): void
    {
        $attributes = $this->generator->formatJson();
        $result = json_encode($attributes);
        $this->assertIsString($result);
        $this->assertSame('{"pact:generator:type":"ProviderState","expression":"\/products\/${id}"}', $result);
    }
}
