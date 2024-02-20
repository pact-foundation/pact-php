<?php

namespace PhpPact\Consumer\Model\Interaction;

trait HandleTrait
{
    private int $handle;

    public function getHandle(): int
    {
        return $this->handle;
    }

    public function setHandle(int $handle): self
    {
        $this->handle = $handle;

        return $this;
    }
}
