<?php

namespace PhpPact\Standalone\StubService;

use Psr\Http\Message\UriInterface;

/**
 * Stub Server configuration interface to allow for simple overrides that are reusable.
 */
interface StubServerConfigInterface
{
    /**
     * @return null|UriInterface url to the pact broker
     */
    public function getBrokerUrl(): ?UriInterface;

    /**
     * @param UriInterface $brokerUrl URL of the pact broker to fetch pacts from
     */
    public function setBrokerUrl(UriInterface $brokerUrl): self;

    /**
     * @param array<string> $dirs Directory of pact files to load
     */
    public function setDirs(array $dirs): self;

    /**
     * @return array<string>
     */
    public function getDirs(): array;

    public function getExtension(): ?string;

    /**
     * @param string $extension File extension to use when loading from a directory (default is json)
     */
    public function setExtension(string $extension): self;

    /**
     * @param array<string> $files Pact file to load
     */
    public function setFiles(array $files): self;

    /**
     * @return array<string>
     */
    public function getFiles(): array;

    public function getLogLevel(): ?string;

    /**
     * @param string $logLevel Log level (defaults to info) [possible values: error, warn, info, debug, trace, none]
     */
    public function setLogLevel(?string $logLevel): self;

    /**
     * @return int the port of the stub service
     */
    public function getPort(): int;

    /**
     * @param int $port Port to run on (defaults to random port assigned by the OS)
     */
    public function setPort(int $port): self;

    /**
     * @return null|string state of the provider
     */
    public function getProviderState(): ?string;

    /**
     * @param string $providerState Provider state regular expression to filter the responses by
     */
    public function setProviderState(string $providerState): self;

    /**
     * @return null|string name of the header
     */
    public function getProviderStateHeaderName(): ?string;

    /**
     * @param string $providerStateHeaderName Name of the header parameter containing the provider state to be used in case multiple matching interactions are found
     */
    public function setProviderStateHeaderName(string $providerStateHeaderName): self;

    /**
     * @return null|string token for the pact broker
     */
    public function getToken(): ?string;

    /**
     * @param null|string $token Bearer token to use when fetching pacts from URLS or Pact Broker
     */
    public function setToken(?string $token): self;

    /**
     * @param array<string> $urls URL of pact file to fetch
     */
    public function setUrls(array $urls): self;

    /**
     * @return array<string>
     */
    public function getUrls(): array;

    /**
     * @return null|string user and password
     */
    public function getUser(): ?string;

    /**
     * @param string $user User and password to use when fetching pacts from URLS or Pact Broker in user:password form
     */
    public function setUser(string $user): self;

    public function isCors(): bool;

    public function setCors(bool $cors): self;

    public function isCorsReferer(): bool;

    public function setCorsReferer(bool $corsReferer): self;

    public function isEmptyProviderState(): bool;

    public function setEmptyProviderState(bool $emptyProviderState): self;

    public function isInsecureTls(): bool;

    public function setInsecureTls(bool $insecureTls): self;

    /**
     * @param array<string> $consumerNames Consumer name to use to filter the Pacts fetched from the Pact broker
     */
    public function setConsumerNames(array $consumerNames): self;

    /**
     * @return array<string>
     */
    public function getConsumerNames(): array;

    /**
     * @param array<string> $providerNames Provider name to use to filter the Pacts fetched from the Pact broker
     */
    public function setProviderNames(array $providerNames): self;

    /**
     * @return array<string>
     */
    public function getProviderNames(): array;

    public function getBaseUri(): UriInterface;
}
