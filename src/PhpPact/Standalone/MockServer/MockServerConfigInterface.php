<?php

namespace PhpPact\Standalone\MockServer;

use Psr\Http\Message\UriInterface;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface MockServerConfigInterface
 */
interface MockServerConfigInterface
{
    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @return bool
     */
    public function isSecure(): bool;

    /**
     * @return UriInterface
     */
    public function getBaseUri(): UriInterface;

    /**
     * @return string
     */
    public function getConsumer(): string;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @return string
     */
    public function getPactDir();

    /**
     * @param string $pactDir
     *
     * @return MockServerConfigInterface
     */
    public function setPactDir(string $pactDir): self;

    /**
     * @return string
     */
    public function getPactFileWriteMode(): string;

    /**
     * @param string $pactFileWriteMode
     *
     * @return MockServerConfigInterface
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self;

    /**
     * @return float
     */
    public function getPactSpecificationVersion();

    /**
     * @param float $pactSpecificationVersion
     *
     * @return MockServerConfigInterface
     */
    public function setPactSpecificationVersion(float $pactSpecificationVersion): self;

    /**
     * @return string
     */
    public function getLog();

    /**
     * @param string $log
     *
     * @return MockServerConfigInterface
     */
    public function setLog(string $log): self;
}
