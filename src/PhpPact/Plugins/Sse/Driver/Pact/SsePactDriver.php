<?php

namespace PhpPact\Plugins\Sse\Driver\Pact;

use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;

class SsePactDriver extends AbstractPluginPactDriver
{
    protected function getPluginName(): string
    {
        return 'sse';
    }
}
