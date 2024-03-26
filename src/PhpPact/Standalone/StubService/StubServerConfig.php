<?php

namespace PhpPact\Standalone\StubService;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Configuration defining the default PhpPact Ruby Standalone server.
 */
class StubServerConfig implements StubServerConfigInterface
{
    private ?UriInterface $brokerUrl         = null;
    private int $port                        = 0;

    private ?string $extension               = null;
    private ?string $logLevel                = null;
    private ?string $providerState           = null;
    private ?string $providerStateHeaderName = null;
    private ?string $token                   = null;
    private ?string $user                    = null;

    /**
     * @var array<string>
     */
    private array $dirs                      = [];
    /**
     * @var array<string>
     */
    private array $files                     = [];
    /**
     * @var array<string>
     */
    private array $urls                      = [];
    /**
     * @var array<string>
     */
    private array $consumerNames             = [];
    /**
     * @var array<string>
     */
    private array $providerNames             = [];

    private bool $cors               = false;
    private bool $corsReferer        = false;
    private bool $emptyProviderState = false;
    private bool $insecureTls        = false;

    public function getBrokerUrl(): ?UriInterface
    {
        return $this->brokerUrl;
    }

    public function setBrokerUrl(UriInterface $brokerUrl): StubServerConfigInterface
    {
        $this->brokerUrl = $brokerUrl;

        return $this;
    }

    public function setDirs(array $dirs): StubServerConfigInterface
    {
        $this->dirs = array_map(fn (string $dir) => $dir, $dirs);

        return $this;
    }

    public function getDirs(): array
    {
        return $this->dirs;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): StubServerConfigInterface
    {
        $this->extension = $extension;

        return $this;
    }

    public function setFiles(array $files): StubServerConfigInterface
    {
        $this->files = array_map(fn (string $file) => $file, $files);

        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setLogLevel(?string $logLevel): StubServerConfigInterface
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    public function getLogLevel(): ?string
    {
        return $this->logLevel;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): StubServerConfigInterface
    {
        $this->port = $port;

        return $this;
    }

    public function getProviderState(): ?string
    {
        return $this->providerState;
    }

    public function setProviderState(string $providerState): StubServerConfigInterface
    {
        $this->providerState = $providerState;

        return $this;
    }

    public function getProviderStateHeaderName(): ?string
    {
        return $this->providerStateHeaderName;
    }

    public function setProviderStateHeaderName(string $providerStateHeaderName): StubServerConfigInterface
    {
        $this->providerStateHeaderName = $providerStateHeaderName;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): StubServerConfigInterface
    {
        $this->token = $token;

        return $this;
    }

    public function setUrls(array $urls): StubServerConfigInterface
    {
        $this->urls = array_map(fn (string $url) => $url, $urls);

        return $this;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): StubServerConfigInterface
    {
        $this->user = $user;

        return $this;
    }

    public function isCors(): bool
    {
        return $this->cors;
    }

    public function setCors(bool $cors): StubServerConfigInterface
    {
        $this->cors = $cors;

        return $this;
    }

    public function isCorsReferer(): bool
    {
        return $this->corsReferer;
    }

    public function setCorsReferer(bool $corsReferer): StubServerConfigInterface
    {
        $this->corsReferer = $corsReferer;

        return $this;
    }

    public function isEmptyProviderState(): bool
    {
        return $this->emptyProviderState;
    }

    public function setEmptyProviderState(bool $emptyProviderState): StubServerConfigInterface
    {
        $this->emptyProviderState = $emptyProviderState;

        return $this;
    }

    public function isInsecureTls(): bool
    {
        return $this->insecureTls;
    }

    public function setInsecureTls(bool $insecureTls): StubServerConfigInterface
    {
        $this->insecureTls = $insecureTls;

        return $this;
    }

    public function setConsumerNames(array $consumerNames): StubServerConfigInterface
    {
        $this->consumerNames = array_map(fn (string $consumerName) => $consumerName, $consumerNames);

        return $this;
    }

    public function getConsumerNames(): array
    {
        return $this->consumerNames;
    }

    public function setProviderNames(array $providerNames): StubServerConfigInterface
    {
        $this->providerNames = array_map(fn (string $providerName) => $providerName, $providerNames);

        return $this;
    }

    public function getProviderNames(): array
    {
        return $this->providerNames;
    }

    public function getBaseUri(): UriInterface
    {
        return new Uri("http://localhost:{$this->getPort()}");
    }
}
