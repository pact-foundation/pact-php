<?php

namespace PhpPact\Consumer\Driver\Interaction\Part;

interface ResponseDriverInterface extends PartDriverInterface
{
    public function withResponse(int $status): self;
}
