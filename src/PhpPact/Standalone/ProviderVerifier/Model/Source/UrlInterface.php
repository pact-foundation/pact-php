<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use Psr\Http\Message\UriInterface;

/**
 * Interface UrlInterface.
 */
interface UrlInterface
{
    /**
     * @return UriInterface
     */
    public function getUrl(): UriInterface;

    /**
     * @param UriInterface $url
     *
     * @return $this
     */
    public function setUrl(UriInterface $url): self;

    /**
     * @return null|string
     */
    public function getToken(): ?string;

    /**
     * @param null|string $token
     *
     * @return $this
     */
    public function setToken(?string $token): self;

    /**
     * @return null|string
     */
    public function getUsername(): ?string;

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): self;

    /**
     * @return null|string
     */
    public function getPassword(): ?string;

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self;
}
