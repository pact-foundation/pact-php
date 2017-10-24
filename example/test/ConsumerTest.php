<?php

require_once(__DIR__ . '/MockApiConsumer.php');

use PHPUnit\Framework\TestCase;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use PhpPact\Mocks\MockHttpService\Models\HttpVerb;
use PhpPact\PactFailureException;
use PhpPact\PactBuilder;
use PhpPact\PactConfig;
use PhpPact\Matchers\Rules\MatcherRuleTypes;
use PhpPact\Matchers\Rules\MatchingRule;


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
        $this->_build = new PactBuilder();
        $this->_build->serviceConsumer(self::CONSUMER_NAME)
            ->hasPactWith(self::PROVIDER_NAME);
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
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);

        // build the response
        $resHeaders = array();
        $resHeaders["Content-Type"] = "application/json";
        $resHeaders["AnotherHeader"] = "my-header";

        $response = new ProviderServiceResponse('200', $resHeaders);
        $response->setBody("{\"msg\" : \"I am the walrus\"}");

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("Basic Get Request")
            ->uponReceiving("A GET request with a base / path and a content type of json")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $clientUnderTest = new MockApiConsumer();
        $clientUnderTest->setMockHost($host);
        $receivedResponse = $clientUnderTest->getBasic("http://localhost");

        // do some asserts on the return
        $this->assertEquals('200', $receivedResponse->getStatusCode(), "Let's make sure we have an OK response");

        // verify the interactions
        $hasException = false;
        try {
            $mockService->verifyInteractions();
        } catch (PactFailureException $e) {
           $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get should verify the interactions and not throw an exception");
    }

    public function testGetWithPath()
    {
        // build the request
        $reqHeaders = array();
        $request = new ProviderServiceRequest(HttpVerb::GET, "/test.php", $reqHeaders);

        $resHeaders = array();
        $response = new ProviderServiceResponse('500', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("There are ids and names - expect three types by default")
            ->uponReceiving("A GET request to get types")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $clientUnderTest->getWithPath("http://localhost");
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a path should verify the interactions and not throw an exception");
    }


    public function testGetWithQuery()
    {
        // build the request
        $reqHeaders = array();
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);
        $request->setQuery("amount=10");

        $resHeaders = array();
        $response = new ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("There are ids and names - expect three types by default")
            ->uponReceiving("A GET request to get types")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $clientUnderTest->getWithQuery("http://localhost");
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a query should verify the interactions and not throw an exception");
    }


    public function testGetWithBody()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);
        $request->setBody('{ "msg" : "I am the walrus" }');

        $resHeaders = array();
        $response = new ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("There are ids and names - expect three types by default")
            ->uponReceiving("A GET request to get types")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $clientUnderTest->getWithBody("http://localhost");
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a body should verify the interactions and not throw an exception");
    }

    /**
     * @test
     */
    public function testGetWithResponseBodyXml()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/xml";
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);
        $request->setQuery("xml=true");

        $resHeaders = array();
        $resHeaders["Content-Type"] = "application/xml";
        $response = new ProviderServiceResponse('200', $resHeaders);
        $body = '<?xml version="1.0" encoding="UTF-8"?><alligator name="Mary" feet="4"><favoriteColor>blue</favoriteColor></alligator>';
        $response->setBody($body);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("There is an XML alligator named Mary")
            ->uponReceiving("A GET request with an XML header")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $clientUnderTest = new MockApiConsumer();
        $clientUnderTest->setMockHost($host);
        $clientUnderTest->getWithResponseBodyXml("http://localhost");

        // verify the interactions
        $hasException = false;
        try {
            $mockService->verifyInteractions();
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get and xml response should verify the interactions and not throw an exception");
    }


    public function testGetWithMultipleRequests()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);
        $request->setBody('{ "msg" : "I am the walrus" }');

        $resHeaders = array();
        $response = new ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("GET with body")
            ->uponReceiving("A GET request with a body")
            ->with($request)
            ->willRespondWith($response);

        // build the second request
        $reqHeaders2 = array();
        $request2 = new ProviderServiceRequest(HttpVerb::GET, "/test.php", $reqHeaders2);

        $resHeaders2 = array();
        $response2 = new ProviderServiceResponse('500', $resHeaders2);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("GET with Path")
            ->uponReceiving("A GET request with a non-trivial path")
            ->with($request2)
            ->willRespondWith($response2);


        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);

            $clientUnderTest->getWithBody("http://localhost");
            $clientUnderTest->getWithPath("http://localhost");
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This get with a body should verify the interactions and not throw an exception");
    }

    /**
     * Run similar test to testGetPath but with a non-defaulted URL
     */
    public function testNonLocalHostUrl()
    {
        $config = new PactConfig();
        $config->setBaseUri("http://google.com", 80, "http");

        // define local build
        $localBuild = new PactBuilder();
        $localBuild->setConfig($config)
            ->serviceConsumer(self::CONSUMER_NAME)
            ->hasPactWith(self::PROVIDER_NAME);


        // build the request
        $reqHeaders = array();
        $request = new ProviderServiceRequest(HttpVerb::GET, "/test.php", $reqHeaders);

        $resHeaders = array();
        $response = new ProviderServiceResponse('500', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $localBuild->getMockService();
        $mockService->given("GET with Path")
            ->uponReceiving("A GET request with a non-trivial path")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        // test that we can overwrite the base url
        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $clientUnderTest->getWithPath("http://google.com");
        } catch (PactFailureException $e) {
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
        $request = new ProviderServiceRequest(HttpVerb::POST, "/", $reqHeaders);
        $request->setBody('{ "type" : "some new type" }');

        $resHeaders = array();
        $response = new ProviderServiceResponse('200', $resHeaders);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("There is something to post to")
            ->uponReceiving("A POST request to save types")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $hasException = false;
        try {
            $clientUnderTest = new MockApiConsumer();
            $clientUnderTest->setMockHost($host);
            $receivedResponse = $clientUnderTest->PostWithBody("http://localhost");
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This POST with a body should verify the interactions and not throw an exception");
    }



    /**
     * @test
     *
     * Run similar test to GetBasic but using Matchers
     */
    public function testGetWithMatcher()
    {
        // build the request
        $reqHeaders = array();
        $reqHeaders["Content-Type"] = "application/json";
        $request = new ProviderServiceRequest(HttpVerb::GET, "/", $reqHeaders);


        // build the response
        $resHeaders = array();
        $resHeaders["Content-Type"] = "application/json";
        $resHeaders["AnotherHeader"] = "my-header";

        $response = new ProviderServiceResponse('200', $resHeaders);
        $response->setBody("{\"msg\" : \"I am almost a walrus\"}");

        $resMatchers = array();
        $resMatchers['$.body.msg'] = new MatchingRule('$.body.msg', array(
            MatcherRuleTypes::RULE_TYPE => MatcherRuleTypes::REGEX_TYPE,
            MatcherRuleTypes::REGEX_PATTERN => 'walrus')
        );
        $resMatchers['$.body.*'] = new MatchingRule('$.body.*', array(
            MatcherRuleTypes::RULE_TYPE => MatcherRuleTypes::OBJECT_TYPE)
        );
        $response->setMatchingRules($resMatchers);

        // build up the expected results and appropriate responses
        $mockService = $this->_build->getMockService();
        $mockService->given("Basic Get Request")
            ->uponReceiving("A GET request with a base / path and a content type of json")
            ->with($request)
            ->willRespondWith($response);

        // build system under test
        $host = $mockService->getHost();

        $clientUnderTest = new MockApiConsumer();
        $clientUnderTest->setMockHost($host);
        $receivedResponse = $clientUnderTest->getBasic("http://localhost");

        // do some asserts on the return
        // this is pointless in this case
        $this->assertEquals('200', $receivedResponse->getStatusCode(), "Let's make sure we have an OK response");

        // verify the interactions
        $hasException = false;
        try {
            $mockService->verifyInteractions();
        } catch (PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "This basic get should verify the interactions by response regex and not throw an exception");
    }
}
