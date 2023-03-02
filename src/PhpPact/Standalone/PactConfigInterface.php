<?php

namespace PhpPact\Standalone;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 * Interface PactConfigInterface.
 */
interface PactConfigInterface
{
    public const MODE_OVERWRITE = 'overwrite';
    public const MODE_MERGE = 'merge';

    /**
     * @return string
     */
    public function getConsumer(): string;

    /**
     * @param string $consumer consumers name
     *
     * @return $this
     */
    public function setConsumer(string $consumer): self;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @param string $provider providers name
     *
     * @return $this
     */
    public function setProvider(string $provider): self;

    /**
     * @return string url to place the pact files when written to disk
     */
    public function getPactDir(): string;

    /**
     * @param null|string $pactDir url to place the pact files when written to disk
     *
     * @return $this
     */
    public function setPactDir(?string $pactDir): self;

    /**
     * @return string pact version
     */
    public function getPactSpecificationVersion(): string;

    /**
     * @param string $pactSpecificationVersion pact version
     *
     * @return $this
     */
    public function setPactSpecificationVersion(string $pactSpecificationVersion): self;

    /**
     * @return null|string directory for log output
     */
    public function getLog(): ?string;

    /**
     * @param string $log directory for log output
     *
     * @return $this
     */
    public function setLog(string $log): self;

    /**
     * @return null|string
     */
    public function getLogLevel(): ?string;

    /**
     * @param string $logLevel
     *
     * @return $this
     */
    public function setLogLevel(string $logLevel): self;

    /**
     * @return string 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     */
    public function getPactFileWriteMode(): string;

    /**
     * @param string $pactFileWriteMode 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     *
     * @return $this
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self;
}
