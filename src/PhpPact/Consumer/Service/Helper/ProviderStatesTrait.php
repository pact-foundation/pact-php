<?php

namespace PhpPact\Consumer\Service\Helper;

use PhpPact\Consumer\Model\ProviderState;

trait ProviderStatesTrait
{
    use FFITrait;
    use InteractionTrait;

    /**
     * @param ProviderState[] $providerStates
     */
    private function setProviderStates(array $providerStates): void
    {
        foreach ($providerStates as $providerState) {
            $this->ffi->pactffi_given($this->getId(), $providerState->getName());
            foreach ($providerState->getParams() as $key => $value) {
                $this->ffi->pactffi_given_with_param($this->getId(), $providerState->getName(), $key, $value);
            }
        }
    }
}
