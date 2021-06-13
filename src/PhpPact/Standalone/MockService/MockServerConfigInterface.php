<?php

namespace PhpPact\Standalone\MockService;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface MockServerConfigInterface.
 */
interface MockServerConfigInterface
{
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
    public function getPactDir(): string;

    /**
     * @param string $pactDir url to place the pact files when written to disk
     *
     * @return MockServerConfigInterface
     */
    public function setPactDir(string $pactDir): self;

    /**
     * @return string pact version
     */
    public function getPactSpecificationVersion(): string;

    /**
     * @param string $pactSpecificationVersion pact specification version
     *
     * @return MockServerConfigInterface
     */
    public function setPactSpecificationVersion(string $pactSpecificationVersion): self;
}
