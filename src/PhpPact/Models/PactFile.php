<?php

namespace PhpPact\Models;

class PactFile extends PactDetails implements \JsonSerializable
{
    public $_metadata;

    CONST SPECIFICATION_VERSION_1 = '1.1.0';
    CONST SPECIFICATION_VERSION_2 = '2.0.0';

    public function __construct()
    {
        $this->_metadata = new \stdClass();
        $this->_metadata->pactSpecificationVersion = static::SPECIFICATION_VERSION_2;
    }

    public function jsonSerialize()
    {
        $obj = parent::jsonSerialize();
        $obj->metadata = $this->_metadata;

        return $obj;
    }

    /**
     * This needs some refactoring.
     *
     * @param $obj
     * @return \stdClass
     */
    public function setMetadata($obj)
    {
        $metadata = new \stdClass();
        if (isset($obj->metadata) && isset($obj->metadata->pactSpecificationVersion)) {
            $metadata =  $obj->metadata;
        }
        elseif (isset($obj->pactSpecification) && isset($obj->metadata->pactSpecification->version)) {
            $metadata->pactSpecificationVersion = $obj->metadata->pactSpecification->version;
        }
        elseif (isset($obj->pactSpecification) && isset($obj->pactSpecification->version)) {
            $metadata->pactSpecificationVersion = $obj->pactSpecification->version;
        }
        elseif (isset($obj->pactSpecificationVersion)) {
            $metadata = $obj;
        }

        $tmpMetadata = (array) $metadata;
        if (empty($tmpMetadata)) {
            throw new \RuntimeException("Metadata is not in the appropriate format");
        }


        $this->_metadata = $metadata;
        return $this->_metadata;
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

    /**
     * By default
     * @param $version
     */
    public function setPactSpecificationVersion($version)
    {
        if (!in_array($version, array(static::SPECIFICATION_VERSION_1, static::SPECIFICATION_VERSION_2))) {
            throw new \Exception("Version of Pact is not supported by Pact-PHP yet: " . $version);
        }

        $this->_metadata->pactSpecificationVersion = $version;
    }

    /**
     * @return mixed
     */
    public function getPactSpecificationVersion()
    {
        return $this->_metadata->pactSpecificationVersion;
    }
}
