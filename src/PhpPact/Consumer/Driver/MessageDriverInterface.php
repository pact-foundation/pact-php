<?php

namespace PhpPact\Consumer\Driver;

use PhpPact\Consumer\Model\Message;

interface MessageDriverInterface extends DriverInterface
{
    public function reify(Message $pact): string;

    public function update(): bool;
}
