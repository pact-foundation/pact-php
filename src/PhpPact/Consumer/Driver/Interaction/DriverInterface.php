<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\ProviderState;

interface DriverInterface
{
    public const REQUEST = 'InteractionPart_Request';
    public const RESPONSE = 'InteractionPart_Response';

    public function newInteraction(string $description): void;

    public function setBody(string $part, ?string $contentType = null, ?string $body = null): void;

    public function setDescription(string $description): void;

    /**
     * @param ProviderState[] $providerStates
     */
    public function setProviderStates(array $providerStates): void;
}
