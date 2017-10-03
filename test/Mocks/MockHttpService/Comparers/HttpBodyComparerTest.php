<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 10/2/2017
 * Time: 1:07 PM
 */

namespace Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Comparers\HttpBodyComparer;
use PhpPact\Mocks\MockHttpService\Matchers\DefaultHttpBodyMatcher;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use PHPUnit\Framework\TestCase;

class HttpBodyComparerTest extends TestCase
{

    /**
     * @test
     */
    public function testCompare()
    {
        $comparer = new HttpBodyComparer();
        $matchers = array();
        $matchers[] = new DefaultHttpBodyMatcher(true);

        // empty body no content type
        // match = true
        $expected = new ProviderServiceRequest("POST", 200, null, "");
        $actual = new ProviderServiceRequest("POST", 200, null, "");
        $results = $comparer->Compare($expected, $actual, $matchers);
        $this->assertEquals(0, $results->HasFailure(), "Empty body, no content-type (1.1 specification");

        // missing body found when empty expected.json
        // match = true
        $expected = new ProviderServiceRequest("POST", 200, null, null);
        $actual = new ProviderServiceRequest("POST", 200, null, false);
        $results = $comparer->Compare($expected, $actual, $matchers);
        $this->assertEquals(0, $results->HasFailure(), "Missing body found, when an empty body was expected (1.1. specification)");


        // missing body no content type.json
        // match = true
        $expected = new ProviderServiceRequest("POST", 200, null, false);
        $actual = new ProviderServiceRequest("POST", 200, null, "{ \"foo\": \"bar\" )");
        $results = $comparer->Compare($expected, $actual, $matchers);
        $this->assertEquals(0, $results->HasFailure(), "Missing body, no content-type (1.1. specification)");

        // non empty body found when empty expected.json
        // match = false
        $expected = new ProviderServiceRequest("POST", 200, null, null);
        $actual = new ProviderServiceRequest("POST", 200, null, "{ \"foo\": \"bar\" )");
        $results = $comparer->Compare($expected, $actual, $matchers);
        $this->assertEquals(1, $results->HasFailure(), "Non empty body found, when an empty body was expected  (1.1. specification)");

        /*
        $hasException = false;
        try {
            $expected = new ProviderServiceRequest("POST", 200, null, "");
            $actual = new ProviderServiceRequest("POST", 200, null, "{ \"foo\": \"bar\" )");
            $results = $comparer->Compare("", "{ \"foo\": \"bar\" )", $matchers);
            $results = $comparer->Compare($expected, $actual, $matchers);
        } catch (\Exception $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException, "Expect a failure to happen");
        */
    }

}
