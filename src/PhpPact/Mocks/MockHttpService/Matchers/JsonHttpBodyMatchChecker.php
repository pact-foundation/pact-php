<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatchCheckerInterface;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Models\HttpMessageInterface;

class JsonHttpBodyMatchChecker implements MatchCheckerInterface
{
    const PATH_PREFIX = 'body';

    private $_allowExtraKeys;

    public function __construct($allowExtraKeysInObjects)
    {
        $this->_allowExtraKeys = $allowExtraKeysInObjects;
    }

    /**
     * Check if expected and actual are empty strings or JSON objects
     *
     * @param $path string
     * @param $expected mixed
     * @param $actual mixed
     * @param $matchingRules[IMatchRules]
     *
     * @throws \Exception
     *
     * @return \PhpPact\Matchers\Checkers\MatcherResult
     */
    public function match($path, $expected, $actual, $matchingRules = [])
    {
        if (!($expected instanceof HttpMessageInterface)) {
            throw new \Exception('Expected is not an instance of IHttpMessage: ' . \print_r($expected, true));
        }

        if (!($actual instanceof HttpMessageInterface)) {
            throw new \Exception('Actual is not an instance of IHttpMessage: ' . \print_r($actual, true));
        }

        if ($this->shouldApplyMatchers($matchingRules)) {
            $jsonPathChecker = new JsonPathMatchChecker();

            return $jsonPathChecker->match($path, $expected, $actual, $matchingRules, $this->_allowExtraKeys);
        }

        $treewalker = new \TreeWalker(
            [
            'debug'      => false,                     //true => return the execution time, false => not
            'returntype' => 'array']              //Returntype = ["obj","jsonstring","array"]
        );

        $actualBody   = $actual->getBody();
        $expectedBody = $expected->getBody();

        if ($expected->getContentType() == 'application/json') {
            if (\is_string($expectedBody)) {
                $expectedBody = \json_decode($expectedBody);
            }
            if (\is_string($actualBody)) {
                $actualBody = \json_decode($actualBody);
            }
        }

        /*
         * According to Pact, arrays have to remain the same regardless of extra keys allowed flag.
         *
         * Objects can have extra nodes but not arrays.   Here, we find all sub arrays and ensure they are the same
         */
        if ($this->_allowExtraKeys == true) {
            $arraysInActual = [];
            $this->findArrays($actualBody, $arraysInActual);

            $arraysInExpected = [];
            $this->findArrays($expectedBody, $arraysInExpected);

            if (\count($arraysInActual) != \count($arraysInExpected)) {
                return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
            }

            for ($i = 0; $i < \count($arraysInExpected); $i++) {
                $testExpected = $arraysInExpected[$i];
                $testActual   = $arraysInActual[$i];
                $diffs        = $treewalker->getdiff($testExpected, $testActual, false);
                $results      = $this->processResults($diffs, $path, false);
                if ($results !== true) {
                    return $results;
                }
            }
        }

        $diffs   = $treewalker->getdiff($expectedBody, $actualBody, false);
        $results = $this->processResults($diffs, $path, $this->_allowExtraKeys);
        if ($results !== true) {
            return $results;
        }

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }

    private function processResults($results, $path, $allowExtraObjectKeys = false)
    {
        if (\count($results['new']) > 0) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
        }
        if (\count($results['edited']) > 0) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
        }
        if (\count($results['removed']) > 0 && $allowExtraObjectKeys == false) {
            return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::AdditionalPropertyInObject));
        }

        return true;
    }

    private function findArrays($obj, &$result)
    {
        if (\is_object($obj)) {
            foreach ($obj as $key => $value) {
                $this->findArrays($value, $result);
            }
        } elseif (\is_array($obj)) {
            $result[] = $obj;
        }
    }

    /**
     * Test if we should apply matching rules to the body
     *
     * @param $matchingRules[MatchingRules]
     *
     * @return bool
     */
    private function shouldApplyMatchers($matchingRules)
    {
        if (\count($matchingRules) > 0) {
            foreach ($matchingRules as $jsonPath => $matchingRule) {
                if (\stripos($jsonPath, '.' . static::PATH_PREFIX) !== false) {
                    return true;
                }
            }
        }

        return false;
    }
}
