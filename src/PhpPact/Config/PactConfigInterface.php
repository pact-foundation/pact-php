<?php

namespace PhpPact\Config;

use PhpPact\Config\Exception\InvalidWriteModeException;
use PhpPact\Config\Enum\WriteMode;
use UnexpectedValueException;

/**
 * Mock Server configuration interface to allow for simple overrides that are reusable.
 */
interface PactConfigInterface
{
    public const DEFAULT_SPECIFICATION_VERSION = '3.0.0';

    /**
     * @deprecated Use WriteMode::OVERWRITE instead
     */
    public const MODE_OVERWRITE = 'overwrite';
    /**
     * @deprecated Use WriteMode::MERGE instead
     */
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

    public function getPactFileWriteMode(): WriteMode;

    /**
     * @throws InvalidWriteModeException If mode is incorrect.
     */
    public function setPactFileWriteMode(string|WriteMode $pactFileWriteMode): self;
}
