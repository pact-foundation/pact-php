<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/5/2017
 * Time: 12:34 PM
 */

namespace PhpPact\Mocks\MockHttpService\Matchers;

class SerializeHttpBodyMatcher implements \PhpPact\Matchers\IMatcher
{
    /**
     * @param $path
     * @param $expected
     * @param $actual
     *
     * @return \PhpPact\Matchers\MatcherResult
     */
    public function Match($path, $expected, $actual)
    {
        if ($actual != null && serialize($expected) == serialize($actual)) {
            return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\SuccessfulMatcherCheck($path));
        }

        return new \PhpPact\Matchers\MatcherResult(new \PhpPact\Matchers\FailedMatcherCheck($path, \PhpPact\Matchers\MatcherCheckFailureType::ValueDoesNotMatch));
    }
}
