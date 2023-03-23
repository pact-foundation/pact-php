<?php

namespace PhpPact\Standalone\MockService;

use PhpPact\Config\PactConfigInterface;
use Psr\Http\Message\UriInterface;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface MockServerConfigInterface.
 */
interface MockServerConfigInterface extends PactConfigInterface
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
}
