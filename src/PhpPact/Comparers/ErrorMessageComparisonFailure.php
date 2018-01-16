<?php

namespace PhpPact\Comparers;

class ErrorMessageComparisonFailure extends ComparisonFailure
{
    public function __construct($errorMessage)
    {
        $this->_result = $errorMessage;
    }
}
