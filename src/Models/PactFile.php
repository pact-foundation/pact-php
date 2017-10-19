<?php

namespace PhpPact\Models;

class PactFile extends PactDetails implements \JsonSerializable
{
    public $_metadata;

    public function __construct()
    {
        $this->_metadata = new \stdClass();
        $this->_metadata->pactSpecificationVersion = '2.0.0';
    }

    public function jsonSerialize()
    {
        $obj = parent::jsonSerialize();
        $obj->metadata = $this->_metadata;

        return $obj;
    }

    public function setMetadata($obj)
    {
        if (isset($obj->metadata) && isset($obj->metadata->pactSpecificationVersion)) {
            $this->_metadata = $obj->metadata;
            return $this->_metadata;
        } elseif (isset($obj->pactSpecificationVersion)) {
            $this->_metadata = $obj;
            return $this->_metadata;
        }

        throw new \RuntimeException("Metadata is not in the appropriate format");
    }

    public function getMetadata()
    {
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
