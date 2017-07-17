<?php

use PhpPact\Mocks\MockHttpService\Models\HttpVerb;
use PHPUnit\Framework\TestCase;

class HttpVerbTest extends TestCase
{
    public function testEnum() {
        $verb = new HttpVerb();

        $this->assertEquals(HttpVerb::GET, $verb->Enum('GeT'), 'Ensure we handle case appropriately.');
        $this->assertEquals(HttpVerb::NOTSET, $verb->Enum('Invalid'), 'Ensure we handle case appropriately.');
    }
}
