<?php

use PhpPact\Models\ProviderStates;
use PHPUnit\Framework\TestCase;

class ProviderStatesTest extends TestCase
{
    public function testAdd()
    {
        $providerStates = new ProviderStates();

        $providerStates->Add("State 1");

        $throwException = false;
        try {
            $providerStates->Add("State 1");
        } catch (Exception $e) {
            $throwException = true;
        }

        $this->assertTrue($throwException, "We expect an exception to be thrown when handlding duplicates");

        $this->assertEquals(1, $providerStates->count(), "We expect one state to already exist");
    }
}
