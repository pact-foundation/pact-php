<?php

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PHPUnit\Framework\TestCase;

class ProviderServiceResponseComparerTest extends TestCase
{
    public function testConvert()
    {
        $comparer = new ProviderServiceResponseComparer();

        $header                 = [];
        $header['Content-Type'] = 'application/json';

        $body = \json_decode('{"msg" : "I am the walrus"}');

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header, $body);

        $results = $comparer->compare($response1, $response2);
        $this->assertFalse($results->hasFailure(), 'We expect these two responses to match.');

        // expect header failure
        $header                 = [];
        $header['Content-Type'] = 'application/json';

        $header2                 = [];
        $header2['Content-Type'] = 'application/json';
        $header2['Accept']       = 'application/json';

        $body = \json_decode('{"msg" : "I am the walrus"}');

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header2, $body);

        $results = $comparer->compare($response2, $response1);
        $this->assertTrue($results->hasFailure(), 'We expect these two responses to not to match as the headers are off.');

        // check status
        $header                 = [];
        $header['Content-Type'] = 'application/json';

        $body = \json_decode('{"msg" : "I am the walrus"}');

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(500, $header, $body);

        $results = $comparer->compare($response1, $response2);
        $this->assertTrue($results->hasFailure(), 'We expect these two responses to not to match as the statuses are off.');

        // check body
        $header                 = [];
        $header['Content-Type'] = 'application/json';

        $body  = \json_decode('{"msg" : "I am the walrus"}');
        $body2 = \json_decode('{"msg" : "I am not the walrus", "id" : 1}');

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header, $body2);

        $results = $comparer->compare($response1, $response2);
        $this->assertTrue($results->hasFailure(), 'We expect these two responses to not to match as the bodies are different.');
    }
}
