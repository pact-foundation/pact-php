<?php

namespace PhpPact\Comparers;

class DiffComparisonFailure extends ComparisonFailure
{
    public function __construct($expected, $actual, $message='')
    {
        $msg           = ($message ? \sprintf('Message: %s', $message) : '');
        $this->_result = \sprintf('Expected: %s, Actual: %s. %s', ($expected ?  \print_r($expected, true) : 'null'), ($actual ? \print_r($actual, true) : 'null'), $msg);
    }
}
