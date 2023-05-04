<?php

namespace PhpPact\Consumer\Driver\Interaction;

interface InteractionDriverInterface extends DriverInterface
{
    /**
     * @param array<string, string[]> $headers
     */
    public function setHeaders(string $part, array $headers): void;

    /**
     * @param array<string, string[]> $query
     */
    public function setQuery(array $query): void;

    public function setRequest(string $method, string $path): void;

    public function setResponse(int $status): void;
}
