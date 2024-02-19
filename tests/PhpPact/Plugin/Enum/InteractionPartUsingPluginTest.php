<?php

namespace PhpPactTest\Plugin\Enum;

use PhpPact\Plugin\Enum\InteractionPartUsingPlugin;
use PHPUnit\Framework\TestCase;

class InteractionPartUsingPluginTest extends TestCase
{
    public function testIsRequest(): void
    {
        $this->assertTrue(InteractionPartUsingPlugin::REQUEST->isRequest());
        $this->assertFalse(InteractionPartUsingPlugin::RESPONSE->isRequest());
        $this->assertTrue(InteractionPartUsingPlugin::BOTH->isRequest());
    }

    public function testIsResponse(): void
    {
        $this->assertFalse(InteractionPartUsingPlugin::REQUEST->isResponse());
        $this->assertTrue(InteractionPartUsingPlugin::RESPONSE->isResponse());
        $this->assertTrue(InteractionPartUsingPlugin::BOTH->isResponse());
    }
}
