<?php

namespace PhpPact\Standalone\ProviderVerifier\Model\Config;

trait PluginDirTrait
{
    private ?string $pluginDir = null;

    public function getPluginDir(): ?string
    {
        return $this->pluginDir;
    }

    public function setPluginDir(?string $pluginDir): self
    {
        $this->pluginDir = $pluginDir;

        return $this;
    }
}
