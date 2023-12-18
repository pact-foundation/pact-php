<?php

namespace PhpPact\Consumer\Model\Interaction;

use JsonException;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

trait StatusTrait
{
    private string $status = '200';

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @throws JsonException
     */
    public function setStatus(int|MatcherInterface $status): self
    {
        $this->status = is_int($status) ? (string) $status : json_encode($status, JSON_THROW_ON_ERROR);

        return $this;
    }
}
