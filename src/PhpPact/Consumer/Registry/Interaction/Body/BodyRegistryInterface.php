<?php

namespace PhpPact\Consumer\Registry\Interaction\Body;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;

interface BodyRegistryInterface
{
    public function withBody(Text|Binary $body): void;
}
