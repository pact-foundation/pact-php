<?php

namespace PhpPact\Models;

class Interaction implements \JsonSerializable
{
    private $_jsonSerializerSettings = 0; // add http://php.net/manual/en/json.constants.php if needed

    /**
     * @var string
     */
    private $_description;

    /**
     * @var string
     */
    private $_providerState;

    function jsonSerialize()
    {
        $obj = new \stdClass();
        $obj->description = $this->_description;
        $obj->provider_state = $this->_providerState;

        return $obj; // Encode this array instead of the current element
    }

    public function AsJsonString()
    {
        return json_encode($this, $this->_jsonSerializerSettings);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $description
     * @return Interaction
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getProviderState()
    {
        return $this->_providerState;
    }

    /**
     * @param string $providerState
     * @return Interaction
     */
    public function setProviderState($providerState)
    {
        $this->_providerState = $providerState;
        return $this;
    }


}