<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/10/2017
 * Time: 9:00 AM
 */

namespace PhpPact\Mocks\MockHttpService;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

class MockProviderHostTest extends TestCase
{

    public function testHandle()
    {

        // happy path testing
        $httpRequest = new \Windwalker\Http\Request\Request();
        $httpRequest = $httpRequest->withUri(new \Windwalker\Uri\PsrUri('http://foo'));
        $httpRequest = $httpRequest->withAddedHeader("Content-Type", "application/json");

        $httpResponse = new \Windwalker\Http\Response\Response();
        $httpResponse = $httpResponse->withStatus('500');
        $httpResponse = $httpResponse->withHeader("Content-Type", "application/json");

        $mockHeaders = array();
        $mockHeaders["Content-Type"] = "application/json";

        $server = (new MockProviderHost());
        $server = $server->whenUri('http://foo');
        $server = $server->andWhenHeaders($mockHeaders);
        $server = $server->return($foo = $httpResponse);
        $server = $server->end();

        $response = $server->handle($httpRequest); // $foo
        /**
         * @var $response \Psr\Http\Message\ResponseInterface
         */
        $responseContentType = $response->getHeader("Content-Type");
        $this->assertEquals("application/json", $responseContentType[0], "Expect Json to the content type");
        $this->assertEquals("500", $response->getStatusCode(), "Expect Json to the content type");


        // unhappy path test - different headers
        $httpRequest = new \Windwalker\Http\Request\Request();
        $httpRequest = $httpRequest->withUri(new \Windwalker\Uri\PsrUri('http://foo'));
        $httpRequest = $httpRequest->withAddedHeader("DifferentHeader", "application/json");

        $httpResponse = new \Windwalker\Http\Response\Response();
        $httpResponse = $httpResponse->withStatus('500');
        $httpResponse = $httpResponse->withHeader("Content-Type", "application/json");

        $mockHeaders = array();
        $mockHeaders["Content-Type"] = "application/json";

        $server = (new MockProviderHost());
        $server = $server->whenUri('http://foo');
        $server = $server->andWhenHeaders($mockHeaders);
        $server = $server->return($foo = $httpResponse);
        $server = $server->end();

        $hasException = false;
        try {
            $response = $server->handle($httpRequest); // $foo
        }
        catch (\Exception $e)
        {
            $hasException = true;
        }
        $this->assertTrue($hasException, "We apply different headers and expect an error to be thrown.");




    }
}
