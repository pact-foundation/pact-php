<?php

namespace PhpPact\FFI;

use PhpPact\Consumer\Driver\Enum\InteractionPart;

interface LibInterface
{
    public function getInteractionPartId(InteractionPart $part): int;
}
