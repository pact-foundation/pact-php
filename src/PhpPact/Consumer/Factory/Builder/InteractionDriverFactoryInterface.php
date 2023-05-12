<?php

namespace PhpPact\Consumer\Factory\Builder;

use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Standalone\MockService\MockServerConfigInterface;

interface InteractionDriverFactoryInterface
{
    public function create(MockServerConfigInterface $config): InteractionDriverInterface;
}
