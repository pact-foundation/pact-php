<?php

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\HttpResponseMessageMapper;
use \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;

use PHPUnit\Framework\TestCase;

class HttpResponseMessageMapperTest extends TestCase
{
    public function testConvert()
    {
        $mapper = new HttpResponseMessageMapper();

        $resHeaders = array();
        $resHeaders["Content-Type"] =  "application/json";
        $resHeaders["Fake-Header"] =  "more-cool-stuff";

        $response = new ProviderServiceResponse(200, $resHeaders);
        $response->setBody("Hello, it's me");

        $httpResponse = $mapper->convert($response);

        $expectedContent = ($response->getHeaders())["Content-Type"];
        $actualContent = ($httpResponse->getHeaders())["Content-Type"][0];

        $this->assertEquals($expectedContent, $actualContent, "Ensure the header content is the same");
        $this->assertEquals(count($response->getHeaders()), count($httpResponse->getHeaders()), "The header count should be the same");
        $this->assertEquals($response->getBody(), (string) $httpResponse->getBody(), "Make sure the body is set.");
    }
}
