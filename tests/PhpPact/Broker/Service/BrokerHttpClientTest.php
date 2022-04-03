<?php

namespace PhpPactTest\Broker\Service;

use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Http\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class BrokerHttpClientTest extends TestCase
{
    public function testAllConsumerUrlsAreExtractedCorrectly()
    {
        $provider         = 'someProvider';
        $expectedPath     = "/pacts/provider/{$provider}/latest";
        $expectedContents = \json_encode(
            [
                '_links' => [
                    'pacts' => [
                        ['href' => 'pact-url-1'],
                        ['href' => 'pact-url-2'],
                    ],
                ],
            ]
        );

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($expectedContents));

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($streamMock));

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($responseMock));

        $uriMock = $this->createMock(UriInterface::class);
        $uriMock->expects($this->once())
            ->method('withPath')
            ->with($this->equalTo($expectedPath))
            ->will($this->returnValue($uriMock));

        $broker = new BrokerHttpClient($httpClientMock, $uriMock);
        $broker->getAllConsumerUrls($provider);
    }
}
