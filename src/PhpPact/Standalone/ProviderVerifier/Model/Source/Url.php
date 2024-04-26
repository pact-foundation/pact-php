<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use Psr\Http\Message\UriInterface;

class Url implements UrlInterface
{
    protected UriInterface $url;
    protected ?string $token     = null;
    protected ?string $username  = null;
    protected ?string $password  = null;

    public function getUrl(): UriInterface
    {
        return $this->url;
    }

    public function setUrl(UriInterface $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
