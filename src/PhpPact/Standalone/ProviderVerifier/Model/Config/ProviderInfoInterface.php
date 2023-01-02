<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

/**
 * Interface ProviderInfoInterface.
 */
interface ProviderInfoInterface
{
    /**
     * @return null|string
     */
    public function getProviderName(): ?string;

    /**
     * @param string $providerName
     *
     * @return $this
     */
    public function setProviderName(string $providerName): self;

    /**
     * @return null|string
     */
    public function getScheme(): ?string;

    /**
     * @param string $scheme
     *
     * @return $this
     */
    public function setScheme(string $scheme): self;

    /**
     * @return null|string
     */
    public function getHost(): ?string;

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host): self;

    /**
     * @return null|int
     */
    public function getPort(): ?int;

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort(int $port): self;

    /**
     * @return null|string
     */
    public function getBasePath(): ?string;

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath(string $basePath): self;
}
