<?php

require_once(__DIR__ . '/MockApiConsumer.php');

use PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{

    /**
     * @var \PhpPact\PactBuilder
     */
    protected $_build;

    const CONSUMER_NAME = "MockApiConsumer";
    const PROVIDER_NAME = "MockApiProvider";


    /**
     * Before each test, rebuild the builder
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_build = new \PhpPact\PactBuilder();
        $this->_build->ServiceConsumer(self::CONSUMER_NAME)
            ->HasPactWith(self::PROVIDER_NAME);
    }

    protected function tearDown()
    {
        parent::tearDown();

        unset($this->_build);
    }

    public function testGetBasic()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/", $reqHeaders);

        // build the response
        $resHeaders = array();
        $resHeaders["Content-Type"] = "application/json";
        $resHeaders["AnotherHeader"] = "my-header";

        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);
        $response->setBody("{\"msg\" : \"I am the walrus\"}");

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("Basic Get Request")
            ->UponReceiving("A GET request with a base / path and a content type of json")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $clientUnderTest = new MockApiConsumer();
        $clientUnderTest->setMockHost($host);
        $receivedResponse = $clientUnderTest->GetBasic("http://localhost");

        // do some asserts on the return
        $this->assertEquals('200', $receivedResponse->getStatusCode(), "Let's make sure we have an OK response");

        // verify the interactions
        $hasException = false;
        try {
            $results = $mockService->VerifyInteractions();
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get should verify the interactions and not throw an exception");
    }

    public function testGetWithPath()
    {
        // build the request
        $reqHeaders = array();
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/test.php", $reqHeaders);

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('500', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("There are ids and names - expect three types by default")
            ->UponReceiving("A GET request to get types")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->GetWithPath("http://localhost");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a path should verify the interactions and not throw an exception");
    }


    public function testGetWithQuery()
    {
        // build the request
        $reqHeaders = array();
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/", $reqHeaders);
        $request->setQuery("amount=10");

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("There are ids and names - expect three types by default")
            ->UponReceiving("A GET request to get types")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->GetWithQuery("http://localhost");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a query should verify the interactions and not throw an exception");
    }


    public function testGetWithBody()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/", $reqHeaders);
        $request->setBody('{ "msg" : "I am the walrus" }');

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("There are ids and names - expect three types by default")
            ->UponReceiving("A GET request to get types")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->GetWithBody("http://localhost");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a body should verify the interactions and not throw an exception");
    }


    public function testGetWithMultipleRequests()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/", $reqHeaders);
        $request->setBody('{ "msg" : "I am the walrus" }');

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("GET with body")
            ->UponReceiving("A GET request with a body")
            ->With($request)
            ->WillRespondWith($response);

        // build the second request
        $reqHeaders2 = array();
        $request2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/test.php", $reqHeaders2);

        $resHeaders2 = array();
        $response2 = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('500', $resHeaders2);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("GET with Path")
            ->UponReceiving("A GET request with a non-trivial path")
            ->With($request2)
            ->WillRespondWith($response2);


        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);

            $receivedBodyResponse = $clientUnderTest->GetWithBody("http://localhost");
            $receivedPathResponse = $clientUnderTest->GetWithPath("http://localhost");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a body should verify the interactions and not throw an exception");
    }

    /**
     * Run similar test to testGetPath but with a non-defaulted URL
     */
    public function testNonLocalHostUrl()
    {
        $config = new \PhpPact\PactConfig();
        $config->setBaseUri("http://google.com", 80, "http");

        // define local build
        $localBuild = new \PhpPact\PactBuilder();
        $localBuild->setConfig($config)
            ->ServiceConsumer(self::CONSUMER_NAME)
            ->HasPactWith(self::PROVIDER_NAME);


        // build the request
        $reqHeaders = array();
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/test.php", $reqHeaders);

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('500', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $localBuild->getMockService();
        $mockService->Given("GET with Path")
            ->UponReceiving("A GET request with a non-trivial path")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        // test that we can overwrite the base url
        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->GetWithPath("http://google.com");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "Even with a non-local host, this get with a path should verify the interactions and not throw an exception");
    }

    /**
     * Run similar test to testGetWithBody but with POST
     */
    public function testPost()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::POST, "/", $reqHeaders);
        $request->setBody('{ "type" : "some new type" }');

        $resHeaders = array();
        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->Given("There is something to post to")
            ->UponReceiving("A POST request to save types")
            ->With($request)
            ->WillRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->PostWithBody("http://localhost");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This POST with a body should verify the interactions and not throw an exception");
    }
}
