<?php

namespace PhpPact\Consumer\Driver\Interaction;

use PhpPact\Consumer\Model\ProviderState;

interface InteractionDriverInterface extends DriverInterface
{
    public function uponReceiving(string $description): void;

    public function withBody(bool $isRequest, ?string $contentType = null, ?string $body = null): void;

    /**
     * @param ProviderState[] $providerStates
     */
    public function given(array $providerStates): void;

    /**
     * @param array<string, string[]> $headers
     */
    public function withHeaders(bool $isRequest, array $headers): void;

    /**
     * @param array<string, string[]> $queryParams
     */
    public function withQueryParameters(array $queryParams): void;

    public function withRequest(string $method, string $path): void;

    public function withResponse(int $status): void;
}
