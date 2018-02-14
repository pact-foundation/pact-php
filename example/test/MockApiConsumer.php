<?php

/**
 * Class MockApiConsumer
 *
 * Example consumer API client.  Note that if you will need to pass in the host  Note
 */
class MockApiConsumer
{
    /**
     * MockApiConsumer constructor.
     *
     * @param null|\PhpPact\Mocks\MockHttpClient $httpClient
     */
    public function __construct($httpClient=null)
    {
        if ($httpClient) {
            $this->setHttpClient($httpClient);
        }
    }

    /**
     * @var \PhpPact\Mocks\MockHttpClient
     */
    private $httpClient;

    /**
     * @param \PhpPact\Mocks\MockHttpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }


    /**
     * Mock out a basic GET
     *
     * @param $uri string
     * @return mixed
     */
    public function getBasic($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("get");


        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

    /**
     * Mock out a basic GET and a xml response
     *
     * @param $uri string
     * @return mixed
     */
    public function getWithResponseBodyXml($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/")
            ->withQuery("xml=true");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/xml")
            ->withMethod("get");


        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

    /**
     *
     * @param $uri string
     * @return mixed
     */
    public function getWithPath($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/test.php");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("get");


        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

    public function getWithQuery($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/")
            ->withQuery("amount=10");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("get");


        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

    public function getWithBody($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("get");

        $msg = '{ "msg" : "I am the walrus" }';
        $httpRequest->getBody()->write($msg);


        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

    public function postWithBody($url)
    {
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath("/");

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("post");

        $msg = '{ "type" : "some new type" }';
        $httpRequest->getBody()->write($msg);

        $response = $this->httpClient->sendRequest($httpRequest);
        return $response;
    }

}
