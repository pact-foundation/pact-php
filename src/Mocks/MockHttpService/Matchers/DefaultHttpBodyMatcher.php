<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PHPUnit\Runner\Exception;

class DefaultHttpBodyMatcher implements \PhpPact\Matchers\IMatcher
{
    CONST PATH = "$..*";

    private $_allowExtraKeys;

    public function __construct($allowExtraKeysInObjects)
    {
        $this->_allowExtraKeys = $allowExtraKeysInObjects;
    }

    public function Match($path, $expected, $actual)
    {
        if (!is_object($expected) && !is_array($expected)) {
            throw new \Exception("Failed to compare objects.   If you are not testing objects, try the SerializeHttpBodyMatcher.");
        }

        if (!$actual || (!is_object($actual) && !is_array($actual))) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
        }

        $treewalker = new \TreeWalker(array(
            "debug" => false,                     //true => return the execution time, false => not
            "returntype" => "array")              //Returntype = ["obj","jsonstring","array"]
        );

        $results = $treewalker->getdiff($expected, $actual, false);

        if (count($results['new']) > 0) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::AdditionalPropertyInObject));
        } else if (count($results['edited']) > 0) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
        } else if (count($results['removed']) > 0 && $this->_allowExtraKeys == false) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::AdditionalPropertyInObject));
        }

        return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\SuccessfulMatcherCheck($path));
    }
}