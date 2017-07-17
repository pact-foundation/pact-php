<?php

namespace PhpPact\Models;

class ProviderStates
{
    public $SetUp;
    public $TearDown;

    private $_providerStates;

    public function __construct($setUp = null, $tearDown = null)
    {
        $this->SetUp = $setUp;
        $this->TearDown = $tearDown;
    }

    public function Add($providerState)
    {
        if (!$this->_providerStates) {
            $this->_providerStates = array();
        }

        if (isset($this->_providerStates["$providerState"])) {
            throw new \InvalidArgumentException(sprintf("providerState '%s' has already been added", $providerState->ProviderStateDescription));
        }

        $this->_providerStates["$providerState"] = $providerState;
    }

    public function count()
    {
        return count($this->_providerStates);
    }

    public function getProviderStates()
    {
        return $this->_providerStates;
    }


    public function Find($providerState)
    {
        if ($providerState == null) {
            throw new \InvalidArgumentException("Please supply a non null providerState");
        }

        if ($this->_providerStates) {
            $description = $providerState;
            if ($providerState instanceof \PhpPact\Models\ProviderState) {
                $description = $providerState->ProviderStateDescription;
            }

            if (isset($this->_providerStates[$description])) {
                $providerStateFromDescription = $this->_providerStates[$description];
                return $providerStateFromDescription;
            }
        }

        return null;
    }

}