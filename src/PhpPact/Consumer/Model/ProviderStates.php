<?php

namespace PhpPact\Consumer\Model;

trait ProviderStates
{
    /**
     * @var array<int, ProviderState>
     */
    private array $providerStates = [];

    /**
     * @return array<int, ProviderState>
     */
    public function getProviderStates(): array
    {
        return $this->providerStates;
    }

    /**
     * @param string $name
     * @param array<string, mixed> $params
     * @param bool   $overwrite
     *
     * @return array<int, ProviderState>
     */
    public function setProviderState(string $name, array $params = [], bool $overwrite = true): array
    {
        $this->addProviderState($name, $params, $overwrite);

        return $this->providerStates;
    }

    /**
     * @param string $name
     * @param array<string, mixed> $params
     * @param bool   $overwrite - if true reset the entire state
     *
     * @return $this
     */
    public function addProviderState(string $name, array $params, bool $overwrite = false): self
    {
        $providerState = new ProviderState();
        $providerState->setName($name);
        $providerState->setParams($params);

        if ($overwrite === true) {
            $this->providerStates = [];
        }

        $this->providerStates[] = $providerState;

        return $this;
    }
}
