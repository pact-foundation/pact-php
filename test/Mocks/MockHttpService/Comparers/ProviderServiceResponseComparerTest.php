<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/10/2017
 * Time: 2:18 PM
 */

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PHPUnit\Framework\TestCase;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;

class ProviderServiceResponseComparerTest extends TestCase
{
    public function testConvert()
    {
        $comparer = new \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer();

        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header, $body);

        $results = $comparer->Compare($response1, $response2);
        $this->assertFalse($results->HasFailure(), "We expect these two responses to match.");

        // expect header failure
        $header = array();
        $header["Content-Type"] = "application/json";

        $header2 = array();
        $header2["Content-Type"] = "application/json";
        $header2["Accept"] = "application/json";


        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header2, $body);

        $results = $comparer->Compare($response2, $response1);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the headers are off.");

        // check status
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(500, $header, $body);

        $results = $comparer->Compare($response1, $response2);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the statuses are off.");

        // check body
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");
        $body2 = \json_decode("{\"msg\" : \"I am not the walrus\", \"id\" : 1}");

        $response1 = new ProviderServiceResponse(200, $header, $body);
        $response2 = new ProviderServiceResponse(200, $header, $body2);

        $results = $comparer->Compare($response1, $response2);
        $this->assertTrue($results->HasFailure(), "We expect these two responses to not to match as the bodies are different.");
    }
}
