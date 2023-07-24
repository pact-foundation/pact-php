<?php

namespace PhpPact\Consumer\Model\Interaction;

trait StatusTrait
{
    private int $status;

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
