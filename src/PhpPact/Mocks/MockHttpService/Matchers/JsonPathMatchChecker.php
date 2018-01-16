<?php

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Matchers\Rules\MatcherRuleTypes;
use PhpPact\Matchers\Rules\MatchingRule;
use PhpPact\Mocks\MockHttpService\Models\HttpMessageInterface;

class JsonPathMatchChecker
{
    /**
     * @param $path
     * @param $expected
     * @param $actual
     * @param $matchingRules array[MatchingRules]
     * @param $allowExtraKeys bool
     * @param $matchingRules string
     * @param mixed $matchingPrefix
     *
     * @return MatcherResult
     */
    public function match($path, $expected, $actual, $matchingRules, $allowExtraKeys = false, $matchingPrefix = '')
    {
        if (\count($matchingRules) < 1) {
            throw new \Exception(\sprintf('JsonPathMatchChecker should not be called if there are no matching rules: %s', $path));
        }

        if (!($expected instanceof HttpMessageInterface)) {
            throw new \Exception('Expected is not an instance of IHttpMessage: ' . \print_r($expected, true));
        }

        if (!($actual instanceof HttpMessageInterface)) {
            throw new \Exception('Actual is not an instance of IHttpMessage: ' . \print_r($actual, true));
        }

        $cloneExpected = clone $expected;
        $cloneActual   = clone $actual;

        if ($cloneExpected->getContentType() == 'application/json') {
            if (\is_string($cloneExpected->getBody())) {
                $expectedBody = $cloneExpected->getBody();
                $expectedBody = \json_decode($expectedBody);
                $cloneExpected->setBody($expectedBody);
            }
            if (\is_string($cloneActual->getBody())) {
                $actualBody = $cloneActual->getBody();
                $actualBody = \json_decode($actualBody);
                $cloneActual->setBody($actualBody);
            }
        }

        $failedReasons = [];
        foreach ($matchingRules as $jsonPath => $matchingRule) {
            /**
             * @var MatchingRule
             */
            $expectedResult = (new \Peekmo\JsonPath\JsonStore($cloneExpected))->get($jsonPath, false, true);
            $actualResult   = (new \Peekmo\JsonPath\JsonStore($cloneActual))->get($jsonPath, false, true);

            $ruleFailedReasons = $this->processMatchingRules($path, $matchingRule, $expectedResult, $actualResult, $jsonPath, $allowExtraKeys);
            $failedReasons     = \array_merge($failedReasons, $ruleFailedReasons);
        }

        if (\count($failedReasons) > 0) {
            return new MatcherResult($failedReasons);
        }

        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }

    /**
     * @param MatchingRule $matchingRule
     * @param $bodyChecker string - better name than $path.  The body checker calling this rule check
     * @param $expected array
     * @param $actual array
     * @param $JSONPath string
     * @param $allowExtraKeys bool
     *
     * @return array
     */
    private function processMatchingRules($bodyChecker, MatchingRule $matchingRule, $expected, $actual, $JSONPath, $allowExtraKeys)
    {
        $ruleFailedReasons = [];

        // if not match type is set, we look at array counts
        if (($matchingRule->getMin() || $matchingRule->getMax()) && !$matchingRule->getType()) {
            if ($matchingRule->getMin() && (\count($actual) < $matchingRule->getMin())) {
                // add logger
                // "JSONPath did not match the max number of array elements in the actual results. Rule: " . $JSONPath;

                $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::ValueDoesNotMatch);
            }
            if ($matchingRule->getMax() && (\count($actual) > $matchingRule->getMax())) {
                // add logger
                // "JSONPath did not match the max number of array elements in the actual results. Rule: " . $JSONPath;
                $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::AdditionalPropertyInObject);
            }
        } else {
            if ($matchingRule->getType() == MatcherRuleTypes::OBJECT_TYPE) {
                $typeFailures      = $this->processTypeCheck($bodyChecker, $matchingRule, $expected, $actual, $allowExtraKeys);
                $ruleFailedReasons = \array_merge($ruleFailedReasons, $typeFailures);
            } elseif ($matchingRule->getType() == MatcherRuleTypes::REGEX_TYPE && $matchingRule->getRegexPattern()) {
                $regexFailures     = $this->processRegexCheck($bodyChecker, $matchingRule, $expected, $actual, $allowExtraKeys);
                $ruleFailedReasons = \array_merge($ruleFailedReasons, $regexFailures);
            }
        }

        return $ruleFailedReasons;
    }

    private function processTypeCheck($bodyChecker, MatchingRule $matchingRule, $expected, $actual, $allowExtraKeys)
    {
        $ruleFailedReasons = [];

        // cycle through a list of results for expected
        // build an array of types
        $expectedTypes = [];
        foreach ($expected as $expectedKey => $expectedSingle) {
            $expectedTypes[\gettype($expectedSingle)] = true;
        }

        // cycle through the list of results for actual

        // if min or max is set, keep track
        // if min or max are not set, throw an exception
        $numOfMatches  = 0;
        $numOfFailures = 0;
        if ($allowExtraKeys) {
            $results = $this->allowExtraKeysCheck($actual, $expected, $expectedTypes, false);
            $numOfMatches += $results['numOfMatches'];
            $numOfFailures += $results['numOfFailures'];
        } else {
            foreach ($actual as $actualKey => $actualSingle) {
                if (isset($expectedTypes[\gettype($actualSingle)])) {
                    $numOfMatches++;
                } else {
                    $numOfFailures++;
                }
            }
        }

        if ($matchingRule->getMin() && $numOfMatches < $matchingRule->getMin()) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath min was set but not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::ValueDoesNotMatch);
        } elseif ($matchingRule->getMax() && $numOfMatches > $matchingRule->getMax()) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath max was set but not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::AdditionalPropertyInObject);
        } elseif (!$matchingRule->getMin() && !$matchingRule->getMax() && $numOfFailures != 0) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath all actual results were not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::ValueDoesNotMatch);
        }

        return $ruleFailedReasons;
    }

    private function processRegexCheck($bodyChecker, MatchingRule $matchingRule, $expected, $actual, $allowExtraKeys)
    {
        $ruleFailedReasons = [];

        // do we really care about expected here?
        $numOfMatches  = 0;
        $numOfFailures = 0;
        $regexPattern  = '/' . \str_replace('/', '\/', $matchingRule->getRegexPattern()) . '/';

        if ($allowExtraKeys) {
            $results = $this->allowExtraKeysCheck($actual, $expected, [], $regexPattern);
            $numOfMatches += $results['numOfMatches'];
            $numOfFailures += $results['numOfFailures'];
        } else {
            foreach ($actual as $actualKey => $actualSingle) {
                $actualMatches     = [];
                $actualRegexResult = \preg_match($regexPattern, $actualSingle, $actualMatches);

                if ($actualRegexResult) {
                    $numOfMatches++;
                } else {
                    $numOfFailures++;
                }
            }
        }
        // this says that if there is at least one type match in the original set and we allow extra keys, we should pass
        // while this is true to pass the test, I do not think it meets the spirit of the test
        // @todo we should ensure we met the right KEYS for the JSON Path match, not just assume are all the same key
        /*
        if (count($actual) > $numOfFailures && $numOfMatches > 0 && $allowExtraKeys) {
            // do nothing
            // gosh this is gross!
        }
        */

        if ($matchingRule->getMin() && $numOfMatches < $matchingRule->getMin()) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath min was set but not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::ValueDoesNotMatch);
        } elseif ($matchingRule->getMax() && $numOfMatches > $matchingRule->getMax()) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath max was set but not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::AdditionalPropertyInObject);
        } elseif (!$matchingRule->getMin() && !$matchingRule->getMax() && $numOfFailures != 0) {
            // add logger
            //$ruleFailedReasons[] = "JSONPath all actual results were not honored by type. Rule: " . $JSONPath;
            $ruleFailedReasons[] = new FailedMatcherCheck($bodyChecker, MatcherCheckFailureType::ValueDoesNotMatch);
        }

        return $ruleFailedReasons;
    }

    /**
     * This use case is particularly complicated, mind bogglingly so.  Please please please help me here!
     *
     * @param array $actual
     * @param array $expected
     * @param mixed $expectedTypes
     * @param mixed $regexPattern
     * @param mixed $isParentArray
     *
     * @return array
     */
    private function allowExtraKeysCheck($actual, $expected, $expectedTypes = [], $regexPattern = false, $isParentArray = false)
    {
        $numOfFailures = 0;
        $numOfMatches  = 0;

        // loosely derive arrays by having the end nodes completely numeric;
        // this seems like a terrible idea, please help me.
        $arraysActual    = [];
        $nonArraysActual = [];

        if ($isParentArray) {
            $arraysActual = $actual;
        } else {
            foreach ($actual as $actualKey => $actualValue) {
                $keys    = \explode('|', $actualKey);
                $lastKey = \array_pop($keys);

                if (\preg_match('/^[0-9]*$/', $lastKey)) {
                    $arraysActual[$actualKey] = $actualValue;
                } else {
                    $nonArraysActual[$actualKey] = $actualValue;
                }
            }
        }

        // now we walk through only arrays
        if ($isParentArray && \count($expectedTypes) > 0 && \count($arraysActual) == 0) {
            $numOfFailures++;
        } else {
            foreach ($arraysActual as $actualKey => $actualValue) {
                // it's a type check
                if ($regexPattern == false) {
                    // if the value is an array by itself, ensure it matches
                    if (\is_array($actualValue)) {
                        $similarExpected      = $this->findSimilarValuesWithSubkeys($expected, $actualKey);
                        $similarExpectedTypes = [];
                        foreach ($similarExpected as $expectedKey => $expectedSingle) {
                            foreach ($expectedSingle as $expectedSingleKey => $expectedSingleValue) {
                                $similarExpectedTypes[\gettype($expectedSingleValue)] = true;
                            }
                        }

                        $results = $this->allowExtraKeysCheck($actualValue, $similarExpected, $similarExpectedTypes, $regexPattern, true);
                        $numOfMatches += $results['numOfMatches'];
                        $numOfFailures += $results['numOfFailures'];
                    }
                    if (isset($expectedTypes[\gettype($actualValue)])) {
                        $numOfMatches++;
                    } else {
                        $numOfFailures++;
                    }
                } else {
                    $actualMatches     = [];
                    $actualRegexResult = \preg_match($regexPattern, $actualValue, $actualMatches);

                    if ($actualRegexResult) {
                        $numOfMatches++;
                    } else {
                        $numOfFailures++;
                    }
                }
            }
        }

        // here we walk through non-arrays
        foreach ($nonArraysActual as $actualKey => $actualValue) {
            // it's a type check
            if ($regexPattern == false) {
                if (isset($expected[$actualKey]) && \gettype($expected[$actualKey]) != \gettype($actualValue)) {
                    $numOfFailures++;
                } elseif (isset($expected[$actualKey])) {
                    $numOfMatches++;
                }
            } else {
                $actualMatches     = [];
                $actualRegexResult = \preg_match($regexPattern, $actualValue, $actualMatches);
                if (isset($expected[$actualKey]) && $actualRegexResult) {
                    $numOfMatches++;
                } elseif (isset($expected[$actualKey])) {
                    $numOfFailures++;
                }
            }
        }

        return [
            'numOfFailures' => $numOfFailures,
            'numOfMatches'  => $numOfMatches
        ];
    }

    private function findSimilarValuesWithSubkeys($assocArr, $keys)
    {
        $explodeKeys = \explode('|', $keys);
        \array_pop($explodeKeys);
        $keys = \implode('|', $explodeKeys);

        $newAssocArr = [];
        foreach ($assocArr as $arrKey => $value) {
            if (\stripos($arrKey, $keys) !== false) {
                $newAssocArr[$arrKey] = $value;
            }
        }

        return $newAssocArr;
    }
}
