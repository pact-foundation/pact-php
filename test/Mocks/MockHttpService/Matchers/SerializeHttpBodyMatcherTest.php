<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/5/2017
 * Time: 12:43 PM
 */

namespace Mocks\MockHttpService\Matchers;

use PHPUnit\Framework\TestCase;

class SerializeHttpBodyMatcherTest extends TestCase
{
    public function testMatch() {
        $matcher = new \PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatcher();

        $result = $matcher->Match("/", 'a', 'a');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful match");

        $result = $matcher->Match("/", 'a', 'b');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a failed string match");

        // test json string
        $result = $matcher->Match("/", '{"id" : 1}', '{"id" : 1}');
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful json string match");

        // test arrays
        $a = array();
        $a[0] = "Zero";
        $a[1] = "One";

        $b = array();
        $b[0] = "Zero";
        $b[1] = "One";

        $result = $matcher->Match("/", $a , $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful array match");

        // test objects
        $a = new \stdClass();
        $a->Zero = 0;
        $a->One = array(1, 2);

        $b = new \stdClass();
        $b->Zero = 0;
        $b->One = array(1, 2);

        $result = $matcher->Match("/", $a , $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\SuccessfulMatcherCheck), "This should be a successful object match");

        // test failed objects
        $a = new \stdClass();
        $a->Zero = 0;
        $a->One = array(1, 2);

        $b = new \stdClass();
        $b->One = array(1, 3);

        $result = $matcher->Match("/", $a , $b);
        $checks = $result->getMatcherChecks();
        $this->assertTrue(($checks[0] instanceof \PhpPact\Matchers\FailedMatcherCheck), "This should be a failed object match");

    }

}
