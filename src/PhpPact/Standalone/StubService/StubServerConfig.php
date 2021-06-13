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
    private ?UriInterface $brokerUrl         = null;
    private array $dirs                      = [];
    private ?string $extension               = null;
    private array $files                     = [];
    private ?string $logLevel                = null;
    private ?int $port                       = null;
    private ?string $providerState           = null;
    private ?string $providerStateHeaderName = null;
    private ?string $token                   = null;
    private array $urls                      = [];
    private ?string $user                    = null;

    private bool $cors               = false;
    private bool $corsReferer        = false;
    private bool $emptyProviderState = false;
    private bool $insecureTls        = false;

    private string $endpoint;

    /**
     * {@inheritdoc}
     */
    public function getBrokerUrl(): ?UriInterface
    {
        return $this->brokerUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setBrokerUrl(UriInterface $brokerUrl): StubServerConfigInterface
    {
        $this->brokerUrl = $brokerUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirs(string ...$dirs): StubServerConfigInterface
    {
        $this->dirs = $dirs;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirs(): array
    {
        return $this->dirs;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtension(string $extension): StubServerConfigInterface
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFiles(string ...$files): StubServerConfigInterface
    {
        $this->files = $files;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogLevel(string $logLevel): StubServerConfigInterface
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogLevel(): ?string
    {
        return $this->logLevel;
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
    public function setPort(int $port): StubServerConfigInterface
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderState(): ?string
    {
        return $this->providerState;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderState(string $providerState): StubServerConfigInterface
    {
        $this->providerState = $providerState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderStateHeaderName(): ?string
    {
        return $this->providerStateHeaderName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderStateHeaderName(string $providerStateHeaderName): StubServerConfigInterface
    {
        $this->providerStateHeaderName = $providerStateHeaderName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(?string $token): StubServerConfigInterface
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrls(string ...$urls): StubServerConfigInterface
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(string $user): StubServerConfigInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCors(): bool
    {
        return $this->cors;
    }

    /**
     * {@inheritdoc}
     */
    public function setCors(bool $cors): StubServerConfigInterface
    {
        $this->cors = $cors;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCorsReferer(): bool
    {
        return $this->corsReferer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCorsReferer(bool $corsReferer): StubServerConfigInterface
    {
        $this->corsReferer = $corsReferer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmptyProviderState(): bool
    {
        return $this->emptyProviderState;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmptyProviderState(bool $emptyProviderState): StubServerConfigInterface
    {
        $this->emptyProviderState = $emptyProviderState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInsecureTls(): bool
    {
        return $this->insecureTls;
    }

    /**
     * {@inheritdoc}
     */
    public function setInsecureTls(bool $insecureTls): StubServerConfigInterface
    {
        $this->insecureTls = $insecureTls;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUri(): UriInterface
    {
        return new Uri("http://localhost:{$this->getPort()}");
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndpoint(string $endpoint): StubServerConfigInterface
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
