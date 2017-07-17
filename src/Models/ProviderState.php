<?php

namespace PhpPact\Models;

class ProviderState
{
    /**
     * @var null
     */
    public $SetUp;
    public $TearDown;
    public $ProviderStateDescription;

    public function __construct($providerState, $setUp = null, $tearDown = null)
    {
        $this->ProviderStateDescription = $providerState;
        $this->SetUp = $setUp;
        $this->TearDown = $tearDown;
    }

    public function __toString()
    {
        return $this->ProviderStateDescription;
    }

}