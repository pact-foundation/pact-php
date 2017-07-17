<?php

namespace PhpPact\Models;

class PactFile extends \PhpPact\Models\PactDetails implements \JsonSerializable
{
    //[JsonProperty(PropertyName = "metadata")]
    public $_metadata;

    public function __construct()
    {
        $this->_metadata = new \stdClass();
        $this->_metadata->pactSpecificationVersion = '1.1.0';
    }

    public function jsonSerialize()
    {
        $obj = parent::jsonSerialize();
        $obj->metadata = $this->_metadata;

        return $obj;
    }

    function setMetadata($obj)
    {
        if (isset($obj->metadata) && isset($obj->metadata->pactSpecificationVersion))
        {
            $this->_metadata = $obj->metadata;
            return $this->_metadata;
        } else if (isset($obj->pactSpecificationVersion))
        {
            $this->_metadata = $obj;
            return $this->_metadata;
        }

        throw new \RuntimeException("Metadata is not in the appropriate format");
    }

    function getMetadata() {
        return $this->_metadata;
    }

    /**
     * Build a standardize file name string for pact file
     *
     * @return string
     */
    public function getFileName()
    {
        return strtolower($this->getConsumer()->getName()) . '-' . strtolower($this->getProvider()->getName()) . '.json';
    }
}