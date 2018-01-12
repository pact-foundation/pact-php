<?php

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper;
use PHPUnit\Framework\TestCase;

class ProviderServiceResponseMapperTest extends TestCase
{
    public function testConvert()
    {
        $mapper = new ProviderServiceResponseMapper();


        $obj = new \stdClass();
        $obj->status = 200;
        $obj->headers = array();

        $providerServiceResponse = $mapper->convert($obj);
        $this->assertEquals(200, $providerServiceResponse->getStatus(), 'Method was set appropriately');
        $this->assertEquals(array(), $providerServiceResponse->getHeaders(), "Empty headers are allowed");

        $obj = new \stdClass();
        $obj->status = 500;
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";

        $providerServiceResponse = $mapper->convert($obj);
        $this->assertEquals(500, $providerServiceResponse->getStatus(), 'Method was set appropriately');
        $this->assertEquals(1, count($providerServiceResponse->getHeaders()), "We expect one header");
        $this->assertFalse($providerServiceResponse->getBody(), "Body has not been set but can be called");

        $obj = new \stdClass();
        $obj->status = 200;
        $obj->headers = array();
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceResponse = $mapper->convert($obj);
        $this->assertEquals("Do not tell me what I can do to my body", $providerServiceResponse->getBody(), "Body has been set to a string");


        $obj = new \stdClass();
        $obj->status = 200;
        $obj->headers = array();
        $obj->body = "{ \"typeId\": 1001, \"name\": \"talking\" }";

        $providerServiceResponse = $mapper->convert($obj);
        $this->assertEquals($obj->body, $providerServiceResponse->getBody(), "Body has not been converted to json.");

        $obj = new \stdClass();
        $obj->status = 200;
        $obj->headers = array();
        $obj->body = \json_decode("{ \"typeId\": 1001, \"name\": \"talking\" }");

        $providerServiceResponse = $mapper->convert($obj);
        $this->assertEquals($obj->body, $providerServiceResponse->getBody(), "Body is an object, which should be allowed.");


        $json = "{ \"status\": 200, \"headers\": { \"Content-Type\": \"application/json\" }, \"body\": { \"segmentTypes\": [ { \"typeId\": 1000, \"name\": \"Negative Segment\" } ] } }";
        $providerServiceResponse = $mapper->convert($json);
        $this->assertEquals(200, $providerServiceResponse->getStatus(), 'Method was set appropriately');
        $this->assertEquals(1, count($providerServiceResponse->getHeaders()), "We expect one header");

        $headers = array();
        $headers["Content-Type"] = "application/json";
        $httpResponse = new \Windwalker\Http\Response\Response('php://memory', 500, $headers);
        $providerServiceResponse = $mapper->convert($httpResponse);
        $this->assertEquals(500, $providerServiceResponse->getStatus(), 'Method was set appropriately');
        $this->assertEquals(1, count($providerServiceResponse->getHeaders()), "We expect one header");

        $httpResponse = new \Windwalker\Http\Response\Response();
        $httpResponse = $httpResponse->withStatus(500)
                            ->withAddedHeader("Content-Type", 'application/xml');

        $providerServiceResponse = $mapper->convert($httpResponse);
        $headerResults = $providerServiceResponse->getHeaders();
        $this->assertEquals("application/xml", $headerResults["Content-Type"] , "A content-type header should exist as json");
    }
}
