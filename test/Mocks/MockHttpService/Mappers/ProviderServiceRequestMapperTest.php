<?php

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;
use PHPUnit\Framework\TestCase;

class ProviderServiceRequestMapperTest extends TestCase
{
    public function testConvert()
    {
        $mapper = new ProviderServiceRequestMapper();

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = new \stdClass();
        $contentType = "Content-Type";
        $obj->headers->$contentType = "application/json";

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals('/test', $providerServiceRequest->getPath(), 'Path was set appropriately');
        $this->assertEquals(1, count($providerServiceRequest->getHeaders()), "We expect one header");
        $this->assertFalse($providerServiceRequest->getBody(), "Body has not been set but can be called");

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals("Do not tell me what I can do to my body", $providerServiceRequest->getBody(), "Body has been set to a string");

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals("Do not tell me what I can do to my body", $providerServiceRequest->getBody(), "Body has been set to a string");

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->body = "{ \"typeId\": 1001, \"name\": \"talking\" }";

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals($obj->body, $providerServiceRequest->getBody(), "Body has not been converted to json.");

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->body = \json_decode("{ \"typeId\": 1001, \"name\": \"talking\" }");

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals($obj->body, $providerServiceRequest->getBody(), "Body is an object, which should be allowed, and not converted to JSON b/c of the header was not set");

        $obj = new \stdClass();
        $obj->method = 'get';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $json = "{ \"typeId\": 1001, \"name\": \"talking\" }";
        $json = \json_decode($json);
        $json = \json_encode($json);
        $obj->body = $json;

        $providerServiceRequest = $mapper->Convert($obj);
        $this->assertEquals($obj->body, $providerServiceRequest->getBody(), "Body is converted to JSON bc of the header");
    }
}
