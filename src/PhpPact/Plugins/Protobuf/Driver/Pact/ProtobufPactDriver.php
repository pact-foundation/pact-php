<?php

namespace PhpPact\Plugins\Protobuf\Driver\Pact;

use PhpPact\Plugin\Driver\Pact\AbstractPluginPactDriver;

class ProtobufPactDriver extends AbstractPluginPactDriver
{
    protected function getPluginName(): string
    {
        return 'protobuf';
    }
}
