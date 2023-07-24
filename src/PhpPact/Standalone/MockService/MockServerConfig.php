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
     * Port on which to run the service. A value of zero will result in the operating system allocating an available port.
     */
    private int $port = 0;

    /**
     * @var bool
     */
    private bool $secure = false;

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
}
