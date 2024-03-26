<?php

namespace PhpPactTest\Plugins\Protobuf\Driver\Pact;

use PhpPact\Plugins\Protobuf\Driver\Pact\ProtobufPactDriver;
use PhpPactTest\Plugin\Driver\Pact\AbstractPluginPactDriverTestCase;

class ProtobufPactDriverTest extends AbstractPluginPactDriverTestCase
{
    protected function createPactDriver(): ProtobufPactDriver
    {
        return new ProtobufPactDriver($this->client, $this->config);
    }

    protected function getPluginName(): string
    {
        return 'protobuf';
    }
}
