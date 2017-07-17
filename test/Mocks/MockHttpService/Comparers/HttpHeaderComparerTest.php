<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 6/29/2017
 * Time: 2:48 PM
 */

namespace Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Comparers\HttpHeaderComparer;
use PHPUnit\Framework\TestCase;

class HttpHeaderComparerTest extends TestCase
{
    public function testCompare() {

        $comparer = new HttpHeaderComparer();

        // test that expected is a subset of actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $results = $comparer->Compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is a subset of actual");

        // test that expected = actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $results = $comparer->Compare($expectedHeaders, $actualHeaders);
        //$this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is equal to actual");

        // test that expected is a superset of actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $expectedHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";

        $results = $comparer->Compare($expectedHeaders, $actualHeaders);
        //$this->assertTrue($results->HasFailure(), "We do expect a failure here as the expected is a super set to actual");


        // tests classes
        $obj = new \stdClass();

        // test that expected = actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $expectedHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = new \stdClass();
        $actualHeaders->{"Content-Type"} = "application/json";
        $actualHeaders->{"TestHeader"} = "Expect this to be there";

        $results = $comparer->Compare($expectedHeaders, $actualHeaders);
        //$this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is equal to actual.  Testing std class");

    }
}
