<?php

/**
 * Class MockApiConsumer
 *
 * Example consumer API client.  Note that if you will need to pass in the host  Note
 */
class MockApiConsumer
{
    /**
     * @var \PhpPact\Mocks\MockHttpService\MockProviderHost
     */
    private $_mockHost;

    /**
     * @param $host
     */
    public function setMockHost(&$host)
    {
        $this->_mockHost = $host;
    }


    /**
     * Mock out a basic GET
     *
     * @param $uri string
     * @return mixed
     */
    public function GetBasic($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("get");


        $response = $this->sendRequest($httpRequest);
        return $response;
    }

    /**
     *
     * @param $uri string
     * @return mixed
     */
    public function GetWithPath($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/test.php");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("get");


        $response = $this->sendRequest($httpRequest);
        return $response;
    }

    public function GetWithQuery($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/")
            ->withQuery("amount=10");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("get");


        $response = $this->sendRequest($httpRequest);
        return $response;
    }

    public function GetWithBody($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("get");

        $msg = '{ "msg" : "I am the walrus" }';
        $httpRequest->getBody()->write($msg);


        $response = $this->sendRequest($httpRequest);
        return $response;
    }

    public function PostWithBody($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("post");

        $msg = '{ "type" : "some new type" }';
        $httpRequest->getBody()->write($msg);

        $response = $this->sendRequest($httpRequest);
        return $response;
    }

    public function GetWithMultipleRequests($url)
    {
        throw new \Exception("Not implemented");
    }

    /**
     * Encapsulate your calls to the actual api. This allows mock out of server calls
     *
     * @param \Psr\Http\Message\RequestInterface $httpRequest
     * @return callable|null|\Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    private function sendRequest(\Psr\Http\Message\RequestInterface $httpRequest)
    {
        // handle mock server
        if (isset($this->_mockHost)) {
            return $this->_mockHost->handle($httpRequest);
        }

        // make actual call to the client
        throw new \Exception("Since this is a mock api client, there is no 'real' server.  This is where you put your app logic.");
    }
}
