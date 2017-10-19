<?php

namespace PhpPact\Comparers;

class ComparisonResult
{
    private $_message = '';
    private $_failures = array();
    private $_childResults = array();

    public function Failures()
    {
        $localFailures = $this->_failures;


        foreach ($this->ChildResults() as $childComparisonResult) {
            $localFailures = array_merge($childComparisonResult->Failures(), $localFailures);
        }

        return $localFailures;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    public function HasFailure()
    {
        if (count($this->Failures()) > 0) {
            return true;
        }

        return false;
    }

    public function ShallowFailureCount()
    {
        return count($this->_failures);
    }

    public function ChildResults()
    {
        return $this->_childResults;
    }

    public function __construct($message = null)
    {
        $this->_message = $message;
    }

    public function recordFailure($comparisonFailure)
    {
        $this->_failures[] = $comparisonFailure;
    }

    public function addChildResult(&$comparisonResult)
    {
        if ($comparisonResult == null) {
            return;
        }

        $this->_childResults[] = $comparisonResult;
    }
}
