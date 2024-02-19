<?php

namespace PhpPactTest\Consumer\Driver\Enum;

use PhpPact\Consumer\Driver\Enum\InteractionPart;
use PHPUnit\Framework\TestCase;

class InteractionPartTest extends TestCase
{
    public function testIsRequest(): void
    {
        $this->assertTrue(InteractionPart::REQUEST->isRequest());
        $this->assertFalse(InteractionPart::RESPONSE->isRequest());
    }

    public function testIsResponse(): void
    {
        $this->assertFalse(InteractionPart::REQUEST->isResponse());
        $this->assertTrue(InteractionPart::RESPONSE->isResponse());
    }
}
