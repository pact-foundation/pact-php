<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use Psr\Http\Message\UriInterface;

/**
 * Class Url.
 */
class Url implements UrlInterface
{
    protected UriInterface $url;
    protected ?string $token     = null;
    protected ?string $username  = null;
    protected ?string $password  = null;

    /**
     * {@inheritdoc}
     */
    public function getUrl(): UriInterface
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl(UriInterface $url): UrlInterface
    {
        $this->url = $url;

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
    public function setToken(?string $token): UrlInterface
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername(string $username): UrlInterface
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(string $password): UrlInterface
    {
        $this->password = $password;

        return $this;
    }
}
