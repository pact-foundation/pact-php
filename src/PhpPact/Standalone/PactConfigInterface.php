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
     * @return float pact version
     */
    public function getPactSpecificationVersion();

    /**
     * @param float $pactSpecificationVersion pact version
     *
     * @return PactConfigInterface
     */
    public function setPactSpecificationVersion(float $pactSpecificationVersion): self;

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
}
