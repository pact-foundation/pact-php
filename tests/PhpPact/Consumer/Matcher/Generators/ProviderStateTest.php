<?php

namespace PhpPactTest\Consumer\Matcher\Generators;

use PhpPact\Consumer\Matcher\Generators\ProviderState;
use PHPUnit\Framework\TestCase;

class ProviderStateTest extends TestCase
{
    public function testSerialize(): void
    {
        $url = new ProviderState('/products/${id}');
        $this->assertSame(
            '{"expression":"\/products\/${id}","pact:generator:type":"ProviderState"}',
            json_encode($url)
        );
    }
}
