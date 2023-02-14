<?php

namespace PhpPact\Standalone\StubService;

use Psr\Http\Message\UriInterface;

/**
 * Stub Server configuration interface to allow for simple overrides that are reusable.
 * Interface StubServerConfigInterface.
 */
interface StubServerConfigInterface
{
    /**
     * @return string the host of the stub service
     */
    public function getHost(): string;

    /**
     * @param string $host The host of the stub service
     *
     * @return StubServerConfigInterface
     */
    public function setHost(string $host): self;

    /**
     * @return int the port of the stub service
     */
    public function getPort(): int;

    /**
     * @param int $port the port of the stub service
     *
     * @return StubServerConfigInterface
     */
    public function setPort(int $port): self;

    /**
     * @return bool true if https
     */
    public function isSecure(): bool;

    /**
     * @param bool $secure set to true for https
     *
     * @return StubServerConfigInterface
     */
    public function setSecure(bool $secure): self;

    /**
     * @return UriInterface
     */
    public function getBaseUri(): UriInterface;

    /**
     * @return ?string directory for log output
     */
    public function getLog(): ?string;

    /**
     * @param string $log directory for log output
     */
    public function setLog(string $log): self;

    public function getPactLocation(): string;

    public function setPactLocation(string $location): self;

    public function getEndpoint(): string;

    public function setEndpoint(string $endpoint): self;
}
