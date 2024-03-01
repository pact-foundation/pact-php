<?php

namespace PhpPact\Consumer\Model\Interaction;

trait PendingTrait
{
    private ?bool $pending = null;

    public function getPending(): ?bool
    {
        return $this->pending;
    }

    public function setPending(?bool $pending): self
    {
        $this->pending = $pending;

        return $this;
    }
}
