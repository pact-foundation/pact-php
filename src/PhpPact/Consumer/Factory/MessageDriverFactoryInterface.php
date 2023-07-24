<?php

namespace PhpPact\Consumer\Factory;

use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Driver\Interaction\MessageDriverInterface;

interface MessageDriverFactoryInterface
{
    public function create(PactConfigInterface $config): MessageDriverInterface;
}
