<?php

namespace PhpPact\Consumer\Service\Helper;

trait InteractionTrait
{
    private int $interactionId;

    private function getId(): int
    {
        return $this->interactionId;
    }
}
