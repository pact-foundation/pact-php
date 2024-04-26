<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Source;

use Psr\Http\Message\UriInterface;

interface UrlInterface
{
    public function getUrl(): UriInterface;

    public function setUrl(UriInterface $url): static;

    public function getToken(): ?string;

    public function setToken(?string $token): static;

    public function getUsername(): ?string;

    public function setUsername(string $username): static;

    public function getPassword(): ?string;

    public function setPassword(string $password): static;
}
