<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;

/**
 * Trait ProviderInfo.
 */
trait ProviderInfo
{
    private ?string $providerName = null;
    private ?string $scheme       = null;
    private ?string $host         = null;
    private ?int $port            = null;
    private ?string $basePath     = null;

    /**
     * {@inheritdoc}
     */
    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderName(string $providerName): VerifierConfigInterface
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme(string $scheme): VerifierConfigInterface
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host): VerifierConfigInterface
    {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort(int $port): VerifierConfigInterface
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePath(string $basePath): VerifierConfigInterface
    {
        $this->basePath = $basePath;

        return $this;
    }
}
