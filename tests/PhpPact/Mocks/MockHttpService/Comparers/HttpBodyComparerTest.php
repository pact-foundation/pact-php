<?php

namespace Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Comparers\HttpBodyComparer;
use PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatchChecker;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use PHPUnit\Framework\TestCase;

class HttpBodyComparerTest extends TestCase
{
    /**
     * @test
     */
    public function testCompare()
    {
        $comparer   = new HttpBodyComparer();
        $matchers   = [];
        $matchers[] = new JsonHttpBodyMatchChecker(true);

        // empty body no content type
        // match = true
        $expected = new ProviderServiceRequest('POST', 200, null, '');
        $actual   = new ProviderServiceRequest('POST', 200, null, '');
        $results  = $comparer->compare($expected, $actual);
        $this->assertEquals(0, $results->hasFailure(), 'Empty body, no content-type (1.1 specification');

        // missing body found when empty expected.json
        // match = true
        $expected = new ProviderServiceRequest('POST', 200, null, null);
        $actual   = new ProviderServiceRequest('POST', 200, null, false);
        $results  = $comparer->compare($expected, $actual);
        $this->assertEquals(0, $results->hasFailure(), 'Missing body found, when an empty body was expected (1.1. specification)');

        // missing body no content type.json
        // match = true
        $expected = new ProviderServiceRequest('POST', 200, null, false);
        $actual   = new ProviderServiceRequest('POST', 200, null, '{ "foo": "bar" )');
        $results  = $comparer->compare($expected, $actual);
        $this->assertEquals(0, $results->hasFailure(), 'Missing body, no content-type (1.1. specification)');

        // non empty body found when empty expected.json
        // match = false
        $expected = new ProviderServiceRequest('POST', 200, null, null);
        $actual   = new ProviderServiceRequest('POST', 200, null, '{ "foo": "bar" )');
        $results  = $comparer->compare($expected, $actual);
        $this->assertEquals(1, $results->hasFailure(), 'Non empty body found, when an empty body was expected  (1.1. specification)');
    }
}
