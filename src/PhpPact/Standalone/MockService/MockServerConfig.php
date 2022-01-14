<?php

namespace PhpPact\Standalone\MockService;

use Composer\Semver\VersionParser;
use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\PactConfigInterface;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 * Class MockServerConfig.
 */
class MockServerConfig implements MockServerConfigInterface, PactConfigInterface
{
    /**
     * Host on which to bind the service.
     *
     * @var string
     */
    private $host = 'localhost';

    /**
     * Port on which to run the service.
     *
     * @var int
     */
    private $port = 7200;

    /**
     * @var bool
     */
    private $secure = false;

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
     * @var string
     */
    private $pactSpecificationVersion;

    /**
     * File to which to log output.
     *
     * @var string
     */
    private $log;

    /** @var bool */
    private $cors = false;

    /**
     * The max allowed attempts the mock server has to be available in. Otherwise it is considered as sick.
     *
     * @var int
     */
    private $healthCheckTimeout;

    /**
     * The seconds between health checks of mock server
     *
     * @var int
     */
    private $healthCheckRetrySec;
    private $logLevel;

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
    public function setHost(string $host): MockServerConfigInterface
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
    public function setPort(int $port): MockServerConfigInterface
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
    public function setSecure(bool $secure): MockServerConfigInterface
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
     * {@inheritdoc}
     */
    public function getPactSpecificationVersion()
    {
        return $this->pactSpecificationVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPactSpecificationVersion($pactSpecificationVersion): PactConfigInterface
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

    /**
     * {@inheritdoc}
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogLevel(string $logLevel): PactConfigInterface
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

    public function setCors($flag): MockServerConfigInterface
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

    /**
     * {@inheritdoc}
     */
    public function setHealthCheckTimeout($timeout): MockServerConfigInterface
    {
        $this->healthCheckTimeout = $timeout;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHealthCheckTimeout(): int
    {
        return $this->healthCheckTimeout;
    }

    /**
     * {@inheritdoc}
     */
    public function setHealthCheckRetrySec($seconds): MockServerConfigInterface
    {
        $this->healthCheckRetrySec = $seconds;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHealthCheckRetrySec(): int
    {
        return $this->healthCheckRetrySec;
    }
}
