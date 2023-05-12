<?php

namespace PhpPact\Standalone\MockService;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Config\PactConfig;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 */
class MockServerConfig extends PactConfig implements MockServerConfigInterface
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

    private bool $cors = false;

    /**
     * The max allowed attempts the mock server has to be available in. Otherwise it is considered as sick.
     */
    private int $healthCheckTimeout;

    /**
     * The seconds between health checks of mock server
     */
    private int $healthCheckRetrySec;

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

    public function setHealthCheckTimeout(int $timeout): MockServerConfigInterface
    {
        $this->healthCheckTimeout = $timeout;

        return $this;
    }

    public function getHealthCheckTimeout(): int
    {
        return $this->healthCheckTimeout;
    }

    public function setHealthCheckRetrySec(int $seconds): MockServerConfigInterface
    {
        $this->healthCheckRetrySec = $seconds;

        return $this;
    }

    public function getHealthCheckRetrySec(): int
    {
        return $this->healthCheckRetrySec;
    }
}
