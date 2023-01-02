<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

interface CallingAppInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getVersion(): ?string;

    public function setVersion(?string $version): self;
}
