<?php

namespace PhpPact\Consumer\Service\Helper;

use PhpPact\Consumer\Model\ProviderState;

trait DescriptionTrait
{
    use FFITrait;
    use InteractionTrait;

    private function setDescription(string $description): void
    {
        $this->ffi->pactffi_upon_receiving($this->getId(), $description);
    }
}
