<?php

namespace PhpPact\Comparers;

class ComparisonFailure
{
    /**
     * @var string describing the failure.
     */
    protected $_result;

    public function getResult()
    {
        return $this->_result;
    }
}