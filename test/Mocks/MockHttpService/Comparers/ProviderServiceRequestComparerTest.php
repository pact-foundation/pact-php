<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/10/2017
 * Time: 3:20 PM
 */

namespace PhpPact\Mocks\MockHttpService\Comparers;

use PHPUnit\Framework\TestCase;

class ProviderServiceRequestComparerTest extends TestCase
{
    public function testConvert()
    {
        $comparer = new \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceRequestComparer();

        // happy path
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $request1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body);
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body);

        $results = $comparer->Compare($request1, $request2);
        $this->assertFalse($results->HasFailure(), "We expect these two requests to match" );

        // expect headers to be off
        $header = array();
        $header["Content-Type"] = "application/json";
        $header["NewHeader"] = "nuffSaid";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $header2 = array();
        $header2["Content-Type"] = "application/json";


        $request1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body);
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header2, $body);

        $results = $comparer->Compare($request1, $request2);
        $this->assertTrue($results->HasFailure(), "We expect these two requests to differ by the header. Note that the actual can have more header entries than the expected." );

        // expect path to be off
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $request1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/old", $header, $body);
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/new", $header, $body);

        $results = $comparer->Compare($request1, $request2);
        $this->assertTrue($results->HasFailure(), "We expect these two requests to differ by path" );

        // expect method to be off
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");

        $request1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body);
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("POST", "/", $header, $body);

        $results = $comparer->Compare($request1, $request2);
        $this->assertTrue($results->HasFailure(), "We expect these two requests to differ by method" );

        // expect body to be off
        $header = array();
        $header["Content-Type"] = "application/json";

        $body = \json_decode("{\"msg\" : \"I am the walrus\"}");
        $body2 = \json_decode("{\"msg\" : \"I am not the walrus. This is me.\", \"id\" : 2}");

        $request1 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body);
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest("GET", "/", $header, $body2);

        $results = $comparer->Compare($request1, $request2);
        $this->assertTrue($results->HasFailure(), "We expect these two requests to differ by body" );
    }
}
