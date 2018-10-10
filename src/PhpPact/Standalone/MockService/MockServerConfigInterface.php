<?php

namespace PhpPact\Standalone\MockService;

use Psr\Http\Message\UriInterface;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface MockServerConfigInterface.
 */
interface MockServerConfigInterface
{
    /**
     * @return string the host of the mock service
     */
    public function getHost(): string;

    /**
     * @param string $host The host of the mock service
     *
     * @return MockServerConfigInterface
     */
    public function setHost(string $host): self;

    /**
     * @return int the port of the mock service
     */
    public function getPort(): int;

    /**
     * @param int $port the port of the mock service
     *
     * @return MockServerConfigInterface
     */
    public function setPort(int $port): self;

    /**
     * @return bool true if https
     */
    public function isSecure(): bool;

    /**
     * @param bool $secure set to true for https
     *
     * @return MockServerConfigInterface
     */
    public function setSecure(bool $secure): self;

    /**
     * @return UriInterface
     */
    public function getBaseUri(): UriInterface;

    /**
     * @return string 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     */
    public function getPactFileWriteMode(): string;

    /**
     * @param string $pactFileWriteMode 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     *
     * @return MockServerConfigInterface
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self;

    /**
     * @return bool
     */
    public function hasCors(): bool;

    /**
     * @param bool|string $flag
     *
     * @return MockServerConfigInterface
     */
    public function setCors($flag): self;

    /**
     * @param int $timeout
     *
     * @return MockServerConfigInterface
     */
    public function setHealthCheckTimeout($timeout): self;

    /**
     * @return int
     */
    public function getHealthCheckTimeout(): int;
}
