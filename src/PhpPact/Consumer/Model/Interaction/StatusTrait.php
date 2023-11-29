<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;

trait StatusTrait
{
    private string $status = '200';

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param int|array<string, mixed> $status
     *
     * @throws JsonException
     */
    public function setStatus(int|array $status): self
    {
        $this->status = is_array($status) ? json_encode($status, JSON_THROW_ON_ERROR) : (string) $status;

        return $this;
    }
}
