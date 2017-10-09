<?php

namespace PhpPact\Matchers;

class MatcherResult
{
    private $_matcherChecks;

    public function __construct($matcherCheck)
    {
        if (is_array($matcherCheck)) {
            $this->_matcherChecks = $matcherCheck;
        } else {
            $this->_matcherChecks = array();
            $this->_matcherChecks[] = $matcherCheck;
        }
    }

    /**
     * @return array
     */
    public function getMatcherChecks()
    {
        return $this->_matcherChecks;
    }

    /**
     * @param array $matcherChecks
     */
    public function setMatcherChecks(array $matcherChecks)
    {
        $this->_matcherChecks = $matcherChecks;
    }
}
