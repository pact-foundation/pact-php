<?php

namespace PhpPact\Standalone\MockService;

use Composer\Semver\VersionParser;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\PactConfigInterface;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 */
class MockServerConfig implements MockServerConfigInterface, PactConfigInterface
{
    /**
     * Host on which to bind the service.
     */
    private string $host = 'localhost';

    /**
     * Port on which to run the service.
     */
    private int $port = 7200;

    private bool $secure = false;

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
     * `overwrite` or `merge`. Use `merge` when running multiple mock service
     * instances in parallel for the same consumer/provider pair. Ensure the
     * pact file is deleted before running tests when using this option so that
     * interactions deleted from the code are not maintained in the file.
     */
    private string $pactFileWriteMode = 'overwrite';

    /**
     * The pact specification version to use when writing the pact. Note that only versions 1 and 2 are currently supported.
     */
    private string $pactSpecificationVersion;

    /**
     * File to which to log output.
     */
    private ?string $log = null;

    private bool $cors = false;

    /**
     * The max allowed attempts the mock server has to be available in. Otherwise it is considered as sick.
     */
    private int $healthCheckTimeout = 100;

    /**
     * The seconds between health checks of mock server
     */
    private float $healthCheckRetrySec = 0.1;

    private ?string $logLevel = null;

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecure(bool $secure): self
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUri(): UriInterface
    {
        $protocol = $this->secure ? 'https' : 'http';

        return new Uri("{$protocol}://{$this->getHost()}:{$this->getPort()}");
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
    public function getPactFileWriteMode(): string
    {
        return $this->pactFileWriteMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactFileWriteMode(string $pactFileWriteMode): self
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
     * {@inheritdoc}
     */
    public function getPactSpecificationVersion(): string
    {
        return $this->pactSpecificationVersion;
    }

    /**
     * {@inheritdoc}
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

    public function getLogLevel(): ?string
    {
        return $this->logLevel;
    }

    public function setLogLevel(string $logLevel): self
    {
        $logLevel = \strtoupper($logLevel);
        if (!\in_array($logLevel, ['DEBUG', 'INFO', 'WARN', 'ERROR'])) {
            throw new \InvalidArgumentException('LogLevel ' . $logLevel . ' not supported.');
        }
        $this->logLevel = $logLevel;

        return $this;
    }

    public function hasCors(): bool
    {
        return $this->cors;
    }

    public function setCors(mixed $flag): self
    {
        if ($flag === 'true') {
            $this->cors = true;
        } elseif ($flag === 'false') {
            $this->cors = false;
        } else {
            $this->cors = (bool) $flag;
        }

        return $this;
    }

    public function setHealthCheckTimeout(int $timeout): self
    {
        $this->healthCheckTimeout = $timeout;

        return $this;
    }

    public function getHealthCheckTimeout(): int
    {
        return $this->healthCheckTimeout;
    }

    public function setHealthCheckRetrySec(float $seconds): self
    {
        $this->healthCheckRetrySec = $seconds;

        return $this;
    }

    public function getHealthCheckRetrySec(): float
    {
        return $this->healthCheckRetrySec;
    }
}
