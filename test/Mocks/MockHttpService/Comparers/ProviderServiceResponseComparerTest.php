<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/10/2017
 * Time: 2:18 PM
 */

namespace PhpPact\Mocks\MockHttpService\Comparers;



use PHPUnit\Framework\TestCase;

class ProviderServiceResponseComparerTest extends TestCase
{

    public function testConvert()
    {
        $comparer = new \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer();

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}", true);

        $response1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body);
        $response2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body);

        $results = $comparer->Compare($response1, $response2);
        $this->assertFalse($results->HasFailure(), "We expect these two responses to match." );

        // expect headers failure
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $headers2 = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}", true);

        $response1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body);
        $response2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers2, $body);

        $results = $comparer->Compare($response2, $response1);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the headers are off." );

        // check status
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}", true);

        $response1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body);
        $response2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(500, $headers, $body);

        $results = $comparer->Compare($response1, $response2);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the statuses are off." );

        // check body
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}", true);
        $body2 = \json_decode("{\"msg\" : \"I am not the walrus\", \"id\" : 1}", true);

        $response1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body);
        $response2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $headers, $body2);

        $results = $comparer->Compare($response1, $response2);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the bodies are different." );
    }
}
