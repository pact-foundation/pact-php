<?php

namespace PhpPact\Matchers;

class FailedMatcherCheck extends MatcherCheck
{
    private $_failureType;

    public function __construct($path, $failureType)
    {
        $this->setPath($path);
        $this->_failureType = $failureType;
    }

    /**
     * @return mixed
     */
    public function getFailureType()
    {
        return $this->_failureType;
    }

    /**
     * @param mixed $failureType
     */
    public function setFailureType($failureType)
    {
        $this->_failureType = $failureType;
    }
}