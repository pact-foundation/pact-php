<?php

namespace PhpPact\Standalone\MockService;

/**
 * Class ConsumerConfig.
 */
class MockServerConfig implements MockServerConfigInterface
{
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
    private string $pactDir;

    /**
     * The pact specification version to use when writing the pact.
     */
    private string $pactSpecificationVersion;

    /**
     * BuilderConfig constructor.
     */
    public function __construct()
    {
        $this->pactDir = \sys_get_temp_dir();
    }

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
    public function setConsumer(string $consumer): MockServerConfigInterface
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
    public function setProvider(string $provider): MockServerConfigInterface
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPactDir(): string
    {
        return $this->pactDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactDir(string $pactDir): MockServerConfigInterface
    {
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
     */
    public function setPactSpecificationVersion(string $pactSpecificationVersion): MockServerConfigInterface
    {
        $this->pactSpecificationVersion = $pactSpecificationVersion;

        return $this;
    }
}
