<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/5/2017
 * Time: 1:22 PM
 */

namespace PhpPact\Mocks\MockHttpService\Matchers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

class DefaultHttpBodyMatcherTest extends TestCase
{
    public function testMatch() {
        $matcher = new \PhpPact\Mocks\MockHttpService\Matchers\DefaultHttpBodyMatcher(false);

        $expected = array();
        $expected[] = "Test";

        $actual = array();
        $actual[] = "Test";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful match");

        // invalid object tests
        $hasException = false;
        try {
            $expected = "str-exp";
            $actual = array();
            $actual[] = "Test";
            $matcher->Match("/", $expected, $actual);
        }catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException, "Expect an exception when expected is a string");


        // if actual is not an object, get a failed to match check
        $result = $matcher->Match("/", array(), "b");
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a successful match");


        // start vetting objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful match");

        // test editted objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a2";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a failed match - editing objects");

        // test new objects
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a2 = "a1";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a failed match - removing objects");

        // test new objects (based on mapper allowKeysInObject = false
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";
        $actual->a2 = "a2";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a failed match - new objects not allowed in current config");

        // test new objects (based on mapper allowKeysInObject = false
        $matcher = new \PhpPact\Mocks\MockHttpService\Matchers\DefaultHttpBodyMatcher(true);
        $expected = new \stdClass();
        $expected->a1 = "a1";

        $actual = new \stdClass();
        $actual->a1 = "a1";
        $actual->a2 = "a2";

        $result = $matcher->Match("/", $expected, $actual);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful match - new objects allowed in current config");
    }

}
