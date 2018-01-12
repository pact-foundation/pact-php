<?php

namespace PhpPact\Comparers;

class ComparisonResult
{
    private $_message      = '';
    private $_failures     = [];
    private $_childResults = [];

    public function __construct($message = null)
    {
        $this->_message = $message;
    }

    public function failures()
    {
        $localFailures = $this->_failures;

        foreach ($this->childResults() as $childComparisonResult) {
            $localFailures = \array_merge($childComparisonResult->failures(), $localFailures);
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

    public function hasFailure()
    {
        if (\count($this->failures()) > 0) {
            return true;
        }

        return false;
    }

    public function shallowFailureCount()
    {
        return \count($this->_failures);
    }

    public function childResults()
    {
        return $this->_childResults;
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
