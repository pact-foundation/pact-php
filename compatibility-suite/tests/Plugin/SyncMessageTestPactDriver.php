<?php

namespace PhpPactTest\CompatibilitySuite\Plugin;

use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;

class SyncMessageTestPactDriver extends AbstractPluginPactDriver
{
    protected function getPluginName(): string
    {
        return 'sync-message-test';
    }
}
