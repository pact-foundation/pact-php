<?php

namespace PhpPact\Standalone\PactMessage;

use PhpPact\Standalone\PactConfigInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 * Class MockServerConfig.
 */
class PactMessageConfig implements PactConfigInterface
{
    /**
     * Consumer name.
     *
     * @var string
     */
    private $consumer;

    /**
     * Provider name.
     *
     * @var string
     */
    private $provider;

    /**
     * Directory to which the pacts will be written.
     *
     * @var string
     */
    private $pactDir;

    /**
     * The pact specification version to use when writing the pact. Note that only versions 1 and 2 are currently supported.
     *
     * @var float
     */
    private $pactSpecificationVersion;

    /**
     * File to which to log output.
     *
     * @var string
     */
    private $log;

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
    public function setConsumer(string $consumer): PactConfigInterface
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
    public function setProvider(string $provider): PactConfigInterface
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactDir()
    {
        if ($this->pactDir === null) {
            return \sys_get_temp_dir();
        }

        return $this->pactDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactDir($pactDir): PactConfigInterface
    {
        $this->pactDir = $pactDir;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactSpecificationVersion()
    {
        return $this->pactSpecificationVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactSpecificationVersion(float $pactSpecificationVersion): PactConfigInterface
    {
        $this->pactSpecificationVersion = $pactSpecificationVersion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * {@inheritdoc}
     */
    public function setLog(string $log): PactConfigInterface
    {
        $this->log = $log;

        return $this;
    }
}
