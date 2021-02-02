<?php

namespace PhpPact\Standalone;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface PactConfigInterface.
 */
interface PactConfigInterface
{
    /**
     * @return string
     */
    public function getConsumer(): string;

    /**
     * @param string $consumer consumers name
     *
     * @return PactConfigInterface
     */
    public function setConsumer(string $consumer): self;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @param string $provider providers name
     *
     * @return PactConfigInterface
     */
    public function setProvider(string $provider): self;

    /**
     * @return string url to place the pact files when written to disk
     */
    public function getPactDir();

    /**
     * @param null|string $pactDir url to place the pact files when written to disk
     *
     * @return PactConfigInterface
     */
    public function setPactDir($pactDir): self;

    /**
     * @return string pact version
     */
    public function getPactSpecificationVersion();

    /**
     * @param string $pactSpecificationVersion pact semver version
     *
     * @return PactConfigInterface
     */
    public function setPactSpecificationVersion($pactSpecificationVersion): self;

    /**
     * @return string directory for log output
     */
    public function getLog();

    /**
     * @param string $log directory for log output
     *
     * @return PactConfigInterface
     */
    public function setLog(string $log): self;

    /**
     * @return null|string
     */
    public function getLogLevel();

    /**
     * @param string $logLevel
     *
     * @return $this
     */
    public function setLogLevel(string $logLevel): self;
}
