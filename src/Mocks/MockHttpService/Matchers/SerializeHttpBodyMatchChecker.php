<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/5/2017
 * Time: 12:34 PM
 */

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\FailedMatcherCheck;
use PhpPact\Matchers\Checkers\MatcherCheckFailureType;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;

class SerializeHttpBodyMatchChecker implements \PhpPact\Matchers\Checkers\IMatchChecker
{
    /**
     * @param $path
     * @param $expected
     * @param $actual
     *
     * @return MatcherResult
     */
    public function Match($path, $expected, $actual)
    {
        if ($actual != null && serialize($expected) == serialize($actual)) {
            return new MatcherResult(new SuccessfulMatcherCheck($path));
        }

        return new MatcherResult(new FailedMatcherCheck($path, MatcherCheckFailureType::ValueDoesNotMatch));
    }
}
