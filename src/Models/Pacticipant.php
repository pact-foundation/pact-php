<?php

namespace PhpPact\Models;

class Pacticipant implements \JsonSerializable
{
    private $_name;

    public function __construct($name = null)
    {
        if ($name) {
            $this->setName($name);
        }
    }

    function jsonSerialize()
    {
        $obj = new \stdClass();
        $obj->name = $this->_name;

        return $obj;
    }

    function setName($obj)
    {
        if (isset($obj->name)) {
            $this->_name = $obj->name;
            return $this->_name;
        } else if (is_string($obj)) {
            $this->_name = $obj;
            return $this->_name;
        }

        throw new \RuntimeException("Name is not in the appropriate format");
    }

    function getName()
    {
        return $this->_name;
    }


}