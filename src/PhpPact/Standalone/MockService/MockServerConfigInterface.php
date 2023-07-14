<?php

namespace PhpPact\Standalone\MockService;

use Psr\Http\Message\UriInterface;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 */
interface MockServerConfigInterface
{
    /**
     * @return string the host of the mock service
     */
    public function getHost(): string;

    /**
     * @param string $host The host of the mock service
     */
    public function setHost(string $host): self;

    /**
     * @return int the port of the mock service
     */
    public function getPort(): int;

    /**
     * @param int $port the port of the mock service
     */
    public function setPort(int $port): self;

    /**
     * @return bool true if https
     */
    public function isSecure(): bool;

    /**
     * @param bool $secure set to true for https
     */
    public function setSecure(bool $secure): self;

    public function getBaseUri(): UriInterface;

    /**
     * @return string 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     */
    public function getPactFileWriteMode(): string;

    /**
     * @param string $pactFileWriteMode 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self;

    public function hasCors(): bool;

    public function setCors(mixed $flag): self;

    public function setHealthCheckTimeout(int $timeout): self;

    public function getHealthCheckTimeout(): int;

    public function setHealthCheckRetrySec(float $seconds): self;

    public function getHealthCheckRetrySec(): float;
}
