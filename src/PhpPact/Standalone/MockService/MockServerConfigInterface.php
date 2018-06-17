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
     * @return string
     */
    public function getConsumer(): string;

    /**
     * @param string $consumer consumers name
     *
     * @return MockServerConfigInterface
     */
    public function setConsumer(string $consumer): self;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @param string $provider providers name
     *
     * @return MockServerConfigInterface
     */
    public function setProvider(string $provider): self;

    /**
     * @return string url to place the pact files when written to disk
     */
    public function getPactDir();

    /**
     * @param null|string $pactDir url to place the pact files when written to disk
     *
     * @return MockServerConfigInterface
     */
    public function setPactDir($pactDir): self;

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
     * @return float pact version
     */
    public function getPactSpecificationVersion();

    /**
     * @param float $pactSpecificationVersion pact version
     *
     * @return MockServerConfigInterface
     */
    public function setPactSpecificationVersion(float $pactSpecificationVersion): self;

    /**
     * @return string directory for log output
     */
    public function getLog();

    /**
     * @param string $log directory for log output
     *
     * @return MockServerConfigInterface
     */
    public function setLog(string $log): self;

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
}
