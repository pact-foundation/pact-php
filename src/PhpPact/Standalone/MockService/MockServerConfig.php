<?php

namespace PhpPact\Standalone\MockService;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 * Class MockServerConfig
 */
class MockServerConfig implements MockServerConfigInterface
{
    /**
     * Host on which to bind the service
     *
     * @var string
     */
    private $host = 'localhost';

    /**
     * Port on which to run the service
     *
     * @var int
     */
    private $port = 7200;

    /**
     * @var bool
     */
    private $secure = false;

    /**
     * Consumer name
     *
     * @var string
     */
    private $consumer;

    /**
     * Provider name
     *
     * @var string
     */
    private $provider;

    /**
     * Directory to which the pacts will be written
     *
     * @var string
     */
    private $pactDir;

    /**
     * `overwrite` or `merge`. Use `merge` when running multiple mock service
     * instances in parallel for the same consumer/provider pair. Ensure the
     * pact file is deleted before running tests when using this option so that
     * interactions deleted from the code are not maintained in the file.
     *
     * @var string
     */
    private $pactFileWriteMode = 'overwrite';

    /**
     * The pact specification version to use when writing the pact. Note that only versions 1 and 2 are currently supported.
     *
     * @var float
     */
    private $pactSpecificationVersion;

    /**
     * File to which to log output
     *
     * @var string
     */
    private $log;

    /**
     * @inheritdoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritdoc
     */
    public function setHost(string $host): MockServerConfigInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function setPort(int $port): MockServerConfigInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure): MockServerConfigInterface
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUri(): UriInterface
    {
        $protocol = $this->secure ? 'https' : 'http';

        return new Uri("{$protocol}://{$this->getHost()}:{$this->getPort()}");
    }

    /**
     * @inheritdoc
     */
    public function getConsumer(): string
    {
        return $this->consumer;
    }

    /**
     * @inheritdoc
     */
    public function setConsumer(string $consumer): MockServerConfigInterface
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @inheritDoc
     */
    public function setProvider(string $provider): MockServerConfigInterface
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPactDir()
    {
        if ($this->pactDir === null) {
            return \sys_get_temp_dir();
        }

        return $this->pactDir;
    }

    /**
     * @inheritDoc
     */
    public function setPactDir(string $pactDir): MockServerConfigInterface
    {
        $this->pactDir = $pactDir;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPactFileWriteMode(): string
    {
        return $this->pactFileWriteMode;
    }

    /**
     * @inheritDoc
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): MockServerConfigInterface
    {
        $options = ['overwrite', 'merge'];

        if (!\in_array($pactFileWriteMode, $options)) {
            $implodedOptions = \implode(', ', $options);

            throw new \InvalidArgumentException("Invalid PhpPact File Write Mode, value must be one of the following: {$implodedOptions}.");
        }

        $this->pactFileWriteMode = $pactFileWriteMode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPactSpecificationVersion()
    {
        return $this->pactSpecificationVersion;
    }

    /**
     * @inheritDoc
     */
    public function setPactSpecificationVersion(float $pactSpecificationVersion): MockServerConfigInterface
    {
        $this->pactSpecificationVersion = $pactSpecificationVersion;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @inheritDoc
     */
    public function setLog(string $log): MockServerConfigInterface
    {
        $this->log = $log;

        return $this;
    }
}
