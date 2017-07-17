<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/7/2017
 * Time: 3:32 PM
 */

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\HttpResponseMessageMapper;
use PHPUnit\Framework\TestCase;

class HttpResponseMessageMapperTest extends TestCase
{
    public function testConvert()
    {
        $mapper = new \PhpPact\Mocks\MockHttpService\Mappers\HttpResponseMessageMapper();

        $resHeaders = array();
        $resHeaders["Content-Type"] =  "application/json";
        $resHeaders["Fake-Header"] =  "more-cool-stuff";

        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse(200, $resHeaders);
        $response->setBody("Hello, it's me");

        $httpResponse = $mapper->Convert($response);

        $expectedContent = ($response->getHeaders())["Content-Type"];
        $actualContent = ($httpResponse->getHeaders())["Content-Type"][0];

        $this->assertEquals($expectedContent,$actualContent, "Ensure the header content is the same");
        $this->assertEquals(count($response->getHeaders()), count($httpResponse->getHeaders()), "The header count should be the same");
        $this->assertEquals($response->getBody(), (string) $httpResponse->getBody(), "Make sure the body is set.");
    }
}
