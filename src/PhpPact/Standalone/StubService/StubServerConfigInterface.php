<?php

namespace PhpPact\Standalone\StubService;

use Psr\Http\Message\UriInterface;

/**
 * Stub Server configuration interface to allow for simple overrides that are reusable.
 * Interface StubServerConfigInterface.
 */
interface StubServerConfigInterface
{
    /**
     * @return null|UriInterface url to the pact broker
     */
    public function getBrokerUrl(): ?UriInterface;

    /**
     * URL of the pact broker to fetch pacts from
     *
     * @param UriInterface $brokerUrl
     *
     * @return StubServerConfigInterface
     */
    public function setBrokerUrl(UriInterface $brokerUrl): self;

    /**
     * @param string ...$dirs Directory of pact files to load
     *
     * @return StubServerConfigInterface
     */
    public function setDirs(string ...$dirs): self;

    /**
     * @return array
     */
    public function getDirs(): array;

    /**
     * @return null|string
     */
    public function getExtension(): ?string;

    /**
     * @param string $extension File extension to use when loading from a directory (default is json)
     *
     * @return StubServerConfigInterface
     */
    public function setExtension(string $extension): self;

    /**
     * @param string ...$files Pact file to load
     *
     * @return StubServerConfigInterface
     */
    public function setFiles(string ...$files): self;

    /**
     * @return array
     */
    public function getFiles(): array;

    /**
     * @return null|string
     */
    public function getLogLevel(): ?string;

    /**
     * @param string $logLevel Log level (defaults to info) [possible values: error, warn, info, debug, trace, none]
     *
     * @return StubServerConfigInterface
     */
    public function setLogLevel(string $logLevel): self;

    /**
     * @return null|int the port of the stub service
     */
    public function getPort(): ?int;

    /**
     * @param int $port Port to run on (defaults to random port assigned by the OS)
     *
     * @return StubServerConfigInterface
     */
    public function setPort(int $port): self;

    /**
     * @return null|string state of the provider
     */
    public function getProviderState(): ?string;

    /**
     * @param string $providerState Provider state regular expression to filter the responses by
     *
     * @return StubServerConfigInterface
     */
    public function setProviderState(string $providerState): self;

    /**
     * @return null|string name of the header
     */
    public function getProviderStateHeaderName(): ?string;

    /**
     * @param string $providerStateHeaderName Name of the header parameter containing the provider state to be used in case multiple matching interactions are found
     *
     * @return StubServerConfigInterface
     */
    public function setProviderStateHeaderName(string $providerStateHeaderName): self;

    /**
     * @return null|string token for the pact broker
     */
    public function getToken(): ?string;

    /**
     * @param null|string $token Bearer token to use when fetching pacts from URLS or Pact Broker
     *
     * @return StubServerConfigInterface
     */
    public function setToken(?string $token): self;

    /**
     * @param string ...$urls URL of pact file to fetch
     *
     * @return StubServerConfigInterface
     */
    public function setUrls(string ...$urls): self;

    /**
     * @return array
     */
    public function getUrls(): array;

    /**
     * @return null|string user and password
     */
    public function getUser(): ?string;

    /**
     * @param string $user User and password to use when fetching pacts from URLS or Pact Broker in user:password form
     *
     * @return StubServerConfigInterface
     */
    public function setUser(string $user): self;

    /**
     * @return bool
     */
    public function isCors(): bool;

    /**
     * @param bool $cors
     *
     * @return StubServerConfigInterface
     */
    public function setCors(bool $cors): self;

    /**
     * @return bool
     */
    public function isCorsReferer(): bool;

    /**
     * @param bool $corsReferer
     *
     * @return StubServerConfigInterface
     */
    public function setCorsReferer(bool $corsReferer): self;

    /**
     * @return bool
     */
    public function isEmptyProviderState(): bool;

    /**
     * @param bool $emptyProviderState
     *
     * @return StubServerConfigInterface
     */
    public function setEmptyProviderState(bool $emptyProviderState): self;

    /**
     * @return bool
     */
    public function isInsecureTls(): bool;

    /**
     * @param bool $insecureTls
     *
     * @return StubServerConfigInterface
     */
    public function setInsecureTls(bool $insecureTls): self;

    /**
     * @return UriInterface
     */
    public function getBaseUri(): UriInterface;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @param string $endpoint
     *
     * @return StubServerConfigInterface
     */
    public function setEndpoint(string $endpoint): self;
}
