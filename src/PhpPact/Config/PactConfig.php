<?php

namespace PhpPact\Config;

use Composer\Semver\VersionParser;

class PactConfig implements PactConfigInterface
{
    use LogLevelTrait;

    /**
     * Consumer name.
     */
    private string $consumer;

    /**
     * Provider name.
     */
    private string $provider;

    /**
     * Directory to which the pacts will be written.
     */
    private ?string $pactDir = null;

    /**
     * The pact specification version to use when writing the pact. Note that only versions 1, 2, 3 and 4 are currently supported.
     */
    private string $pactSpecificationVersion = self::DEFAULT_SPECIFICATION_VERSION;

    /**
     * File to which to log output.
     */
    private ?string $log = null;

    private ?string $logLevel = null;
    /**
     * `overwrite` or `merge`. Use `merge` when running multiple mock service
     * instances in parallel for the same consumer/provider pair. Ensure the
     * pact file is deleted before running tests when using this option so that
     * interactions deleted from the code are not maintained in the file.
     */
    private string $pactFileWriteMode = self::MODE_MERGE;

    /**
     * {@inheritdoc}
     */
    public function getConsumer(): string
    {
        return $this->consumer;
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumer(string $consumer): self
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactDir(): string
    {
        if ($this->pactDir === null) {
            return \sys_get_temp_dir();
        }

        return $this->pactDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactDir(?string $pactDir): self
    {
        if ($pactDir === null) {
            return $this;
        }

        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $pactDir = \str_replace('\\', \DIRECTORY_SEPARATOR, $pactDir);
        }

        $this->pactDir = $pactDir;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactSpecificationVersion(): string
    {
        return $this->pactSpecificationVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \UnexpectedValueException
     */
    public function setPactSpecificationVersion(string $pactSpecificationVersion): self
    {
        /*
         * Parse the version but do not assign it.  If it is an invalid version, an exception is thrown
         */
        $parser = new VersionParser();
        $parser->normalize($pactSpecificationVersion);

        $this->pactSpecificationVersion = $pactSpecificationVersion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLog(): ?string
    {
        return $this->log;
    }

    /**
     * {@inheritdoc}
     */
    public function setLog(string $log): self
    {
        $this->log = $log;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactFileWriteMode(): string
    {
        return $this->pactFileWriteMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self
    {
        $options = [self::MODE_OVERWRITE, self::MODE_MERGE];

        if (!\in_array($pactFileWriteMode, $options)) {
            $implodedOptions = \implode(', ', $options);

            throw new \InvalidArgumentException("Invalid PhpPact File Write Mode, value must be one of the following: {$implodedOptions}.");
        }

        $this->pactFileWriteMode = $pactFileWriteMode;

        return $this;
    }
}
