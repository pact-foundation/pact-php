<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

class CallingApp implements CallingAppInterface
{
    private ?string $name    = null;
    private ?string $version = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): CallingAppInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): CallingAppInterface
    {
        $this->version = $version;

        return $this;
    }
}
