<?php

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\HttpRequestMessageMapper;
use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;
use PHPUnit\Framework\TestCase;

class HttpRequestMessageMapperTest extends TestCase
{
    public function testConvert()
    {
        $mapper = new HttpRequestMessageMapper();

        // test standard
        $obj = new \stdClass();
        $obj->method = 'post';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequestMapper = new ProviderServiceRequestMapper();
        $providerServiceRequest = $providerServiceRequestMapper->convert($obj);
        $httpRequest = $mapper->convert($providerServiceRequest, "http://localhost");

        $this->assertTrue(($httpRequest instanceof \Psr\Http\Message\RequestInterface), "We expect a Psr request");

        $actualHeaders = $httpRequest->getHeaders();
        $this->assertTrue(isset($actualHeaders["Content-Type"]), "We expect one header - content-type");
        $this->assertEquals('/test', $httpRequest->getUri()->getPath(), "Test that path was set appropriately");
        $this->assertFalse(($httpRequest->getUri()->getQuery()? true : false), "Test that query was not set.  Note this is an explicit false check");
        $this->assertEquals($obj->body, (string) $httpRequest->getBody(), "Body is set appropriately");

        // test duplicate headers
        $obj = new \stdClass();
        $obj->method = 'post';
        $obj->path = '/test';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $obj->headers["X-Header-Type"] = "interesting_header";
        $obj->headers["content-type"] = "application/xml"; // ensures we are case sensitive
        $obj->headers["Host"] = 'myhost'; // ensures we are not overwriting this by Windwalker or other libraries

        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequestMapper = new ProviderServiceRequestMapper();
        $providerServiceRequest = $providerServiceRequestMapper->convert($obj);
        $httpRequest = $mapper->convert($providerServiceRequest, "http://localhost");

        $this->assertTrue(($httpRequest instanceof \Psr\Http\Message\RequestInterface), "We expect a Psr request");

        $actualHeaders = $httpRequest->getHeaders();
        $this->assertTrue(isset($actualHeaders["Content-Type"]), "We expect a cased header - Content-Type");
        $this->assertEquals(array_pop($actualHeaders["Content-Type"]), "application/json", "Content-Type to exist and be json");
        $this->assertFalse(isset($actualHeaders["content-type"]), "We expect the lower case header not to exist - content-type");
        $this->assertEquals(array_pop($actualHeaders["Host"]), "myhost", "Ensure it is not overwritten");
        $this->assertEquals(count($actualHeaders), 3, "Only three headers should be set");
        
        
        // test query
        $obj = new \stdClass();
        $obj->method = 'post';
        $obj->path = '/test';
        $obj->query = '?x=1&y=2';
        $obj->headers = array();
        $obj->headers["Content-Type"] = "application/json";
        $obj->body = "Do not tell me what I can do to my body";

        $providerServiceRequestMapper = new ProviderServiceRequestMapper();
        $providerServiceRequest = $providerServiceRequestMapper->convert($obj);
        $httpRequest = $mapper->convert($providerServiceRequest, "http://localhost");

        $this->assertTrue(($httpRequest instanceof \Psr\Http\Message\RequestInterface), "We expect a Psr request");

        $actualHeaders = $httpRequest->getHeaders();
        $this->assertTrue(isset($actualHeaders["Content-Type"]), "We expect one header - content-type");
        $this->assertEquals('/test', $httpRequest->getUri()->getPath(), "Test that path was set appropriately");
        $this->assertEquals("x=1&y=2", $httpRequest->getUri()->getQuery(), "Test that query was set with the ? removed");
        $this->assertEquals($obj->body, (string) $httpRequest->getBody(), "Body is set appropriately");
    }
}
