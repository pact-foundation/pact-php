<?php

namespace PhpPact\Standalone\StubService;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 * Class StubServerConfig.
 */
class StubServerConfig implements StubServerConfigInterface
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
    private $port = 7201;

    /**
     * @var bool
     */
    private $secure = false;

    /**
     * File to which to log output.
     *
     * @var string
     */
    private $log;

    private $pactLocation;
    private $endpoint;

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
    public function setHost(string $host): StubServerConfigInterface
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
    public function setPort(int $port): StubServerConfigInterface
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
    public function setSecure(bool $secure): StubServerConfigInterface
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
    public function getLog()
    {
        return $this->log;
    }

    /**
     * {@inheritdoc}
     */
    public function setLog(string $log): StubServerConfigInterface
    {
        $this->log = $log;

        return $this;
    }

    public function getPactLocation(): string
    {
        return $this->pactLocation;
    }

    public function setPactLocation(string $location)
    {
        $this->pactLocation = $location;

        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
