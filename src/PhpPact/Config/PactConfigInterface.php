<?php

namespace PhpPact\Config;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 */
interface PactConfigInterface
{
    public const DEFAULT_SPECIFICATION_VERSION = '2.0.0';

    public const MODE_OVERWRITE = 'overwrite';
    public const MODE_MERGE = 'merge';

    public function getConsumer(): string;

    /**
     * @param string $consumer consumers name
     */
    public function setConsumer(string $consumer): self;

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
     *
     * @throws UnexpectedValueException
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

    /**
     * @return string 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     */
    public function getPactFileWriteMode(): string;

    /**
     * @param string $pactFileWriteMode 'merge' or 'overwrite' merge means that interactions are added and overwrite means that the entire file is overwritten
     *
     * @throws InvalidArgumentException If mode is incorrect.
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self;
}
