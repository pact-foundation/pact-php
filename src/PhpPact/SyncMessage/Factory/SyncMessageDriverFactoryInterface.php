<?php

namespace PhpPact\SyncMessage\Factory;

use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

interface SyncMessageDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): SyncMessageDriverInterface;
}
