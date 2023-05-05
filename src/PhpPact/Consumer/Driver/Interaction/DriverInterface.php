<?php

namespace PhpPact\Consumer\Driver\Interaction;

interface DriverInterface
{
    public const REQUEST = 'InteractionPart_Request';
    public const RESPONSE = 'InteractionPart_Response';

    public function newInteraction(string $description): void;
}
