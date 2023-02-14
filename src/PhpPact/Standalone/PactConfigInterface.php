<?php

namespace PhpPact\Standalone;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 */
interface PactConfigInterface
{
    /**
     * @return string
     */
    public function getConsumer(): string;

    /**
     * @param string $consumer consumers name
     */
    public function setConsumer(string $consumer): self;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @param string $provider providers name
     */
    public function setProvider(string $provider): self;

    /**
     * @return string url to place the pact files when written to disk
     */
    public function getPactDir(): string;

    /**
     * @param null|string $pactDir url to place the pact files when written to disk
     */
    public function setPactDir(?string $pactDir): self;

    /**
     * @return string pact version
     */
    public function getPactSpecificationVersion(): string;

    /**
     * @param string $pactSpecificationVersion pact semver version
     */
    public function setPactSpecificationVersion(string $pactSpecificationVersion): self;

    /**
     * @return null|string directory for log output
     */
    public function getLog(): ?string;

    /**
     * @param string $log directory for log output
     */
    public function setLog(string $log): self;

    public function getLogLevel(): ?string;

    public function setLogLevel(string $logLevel): self;
}
