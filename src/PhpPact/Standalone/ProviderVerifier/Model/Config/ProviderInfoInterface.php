<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface ProviderInfoInterface
{
    public function getName(): ?string;

    public function setName(string $name): self;

    public function getHost(): ?string;

    public function setHost(string $host): self;

    public function getScheme(): ?string;

    public function setScheme(?string $scheme): self;

    public function getPort(): ?int;

    public function setPort(?int $port): self;

    public function getPath(): ?string;

    public function setPath(?string $path): self;
}
