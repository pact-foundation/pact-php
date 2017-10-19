<?php

namespace Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Comparers\HttpHeaderComparer;
use PHPUnit\Framework\TestCase;

class HttpHeaderComparerTest extends TestCase
{
    public function testCompare()
    {
        $comparer = new HttpHeaderComparer();

        // test that expected is a subset of actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is a subset of actual");

        // test that the lowercase comparison is implemented
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";

        $actualHeaders = array();
        $actualHeaders["coNTent-tYpe"] = "application/json";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure here because it should be a lowercase comparison");


        // test that expected = actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";
        $actualHeaders["TestHeader"] = "Expect this to be there";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is equal to actual");

        // test that expected is a superset of actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $expectedHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = array();
        $actualHeaders["Content-Type"] = "application/json";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertTrue($results->HasFailure(), "We do expect a failure here as the expected is a super set to actual");

        // test that expected = actual
        $expectedHeaders = array();
        $expectedHeaders["Content-Type"] = "application/json";
        $expectedHeaders["TestHeader"] = "Expect this to be there";

        $actualHeaders = new \stdClass();
        $actualHeaders->{"Content-Type"} = "application/json";
        $actualHeaders->{"TestHeader"} = "Expect this to be there";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure here as the expected is equal to actual.  Testing std class");

        // test breaking apart header values by commas and other separators
        $expectedHeaders = array();
        $expectedHeaders["TestHeader"] = "expect, this to be there";

        $actualHeaders = new \stdClass();
        $actualHeaders->{"TestHeader"} = "expect, this to be there";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure as the headers are identical with a comma (following a separate code path");

        // test breaking apart header values by commas and other separators with spaces
        $expectedHeaders = array();
        $expectedHeaders["TestHeader"] = "expect, this space,  to be there";

        $actualHeaders = new \stdClass();
        $actualHeaders->{"TestHeader"} = "expect, this space, to be there";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertFalse($results->HasFailure(), "We do not expect a failure as the headers are identical with more than one comma and extra spaces (following a separate code path");

        // test breaking apart header values by commas and other separators with spaces where order matters
        $expectedHeaders = array();
        $expectedHeaders["TestHeader"] = "a, b, c";

        $actualHeaders = new \stdClass();
        $actualHeaders->{"TestHeader"} = "b, c, a";

        $results = $comparer->compare($expectedHeaders, $actualHeaders);
        $this->assertTrue($results->HasFailure(), "Expect a failure as header value order matters");
    }
}
