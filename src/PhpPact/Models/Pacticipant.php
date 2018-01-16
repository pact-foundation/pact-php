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

    public function jsonSerialize()
    {
        $obj       = new \stdClass();
        $obj->name = $this->_name;

        return $obj;
    }

    public function setName($obj)
    {
        if (isset($obj->name)) {
            $this->_name = $obj->name;

            return $this->_name;
        }
        if (\is_string($obj)) {
            $this->_name = $obj;

            return $this->_name;
        }

        throw new \RuntimeException('Name is not in the appropriate format');
    }

    public function getName()
    {
        return $this->_name;
    }
}
