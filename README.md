# Pact-PHP-Native

PHP version of Pact. Enables consumer driven contract testing, providing a mock service and DSL for the consumer project, and interaction playback and verification for the service provider project.

This is a project to provide Pact functionality completely in PHP. This started as a straight port of 
[Pact.Net](https://github.com/SEEK-Jobs/pact-net) on the 1.1 specification. Overtime, the project adjusted to a more 
PHP way of doing things. 


## Composer
Run `composer require mattersight/phppact`

## Service Consumer

### 1. Build your client
This is either a net new green field client you are writing in PHP or a legacy application. The key part is your client 
will need to inject a "mock server".   To provide Windows support, this project leverages [julienfalque/http-mock
](https://github.com/julienfalque/http-mock).   

```php
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
     * Mock out a basic GET.  Assume it returns some business value to be used in other parts of this mock api consumer/client
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
	
	/*
		Other examples in examples/test/MockApiConsumer.php
	*/
	
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
```

### 2. Write your tests
Create a new test case within your service consumer test project, using whatever test framework you like (in this case we used phpUnit). 
Then implement your tests.

```php
<?php

require_once( __DIR__ . '/MockApiConsumer.php');

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
}
```

### 3. Run the test
Everything should be green


## Service Provider

### 1. Build API
Get an instance of the API up and running.  If your API support PHP's [built-in web server](http://php.net/manual/en/features.commandline.webserver.php), see [this great tutorial](http://tech.vg.no/2013/07/19/using-phps-built-in-web-server-in-your-test-suites/) on 
bootstrapping phpunit to spin up the API, run tests, and tear down the API.   See examples/site/provider.php and 
examples/test/bootstrap.php for a local server on Windows.

### 2. Configure PHP unit
Bootstrap PHPUnit with appropriate composer and autoloaders.   Optionally, add a bootstrap api from the *Build API* section.   

### 3. Tell the provider it needs to honour the pact
```php
<?php

// Pick your PSR client.  Guzzle should work as well.
$httpClient = new \Windwalker\Http\HttpClient();

$pactVerifier->ProviderState("A GET request to get types")
                ->ServiceProvider("MockApiProvider", $httpClient)
                ->HonoursPactWith("MockApiConsumer")
                ->PactUri('../pact/mockapiconsumer-mockapiprovider.json')
                ->Verify();
```

### 4. Run the test
Everything should be green


## Other Notes
This was tested and used on Windows 10.  Below are the versions of PHP:

- PHPUnit 6.2.2
- PHP 7.1.4

## Key Examples

### Provider setUp and tearDown
The setUp and tearDown on a per Provider test basis needs to use closures

```php
<?php

require_once( __DIR__ . '/MockApiConsumer.php');

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public function testPactProviderStateSetupTearDown() 
    {
        $httpClient = new \Windwalker\Http\HttpClient();

        $pactVerifier = new \PhpPact\PactVerifier($uri);

        $setUpFunction = function() {
            $fileName = "mock.json";
            $currentDir = dirname(__FILE__);
            $absolutePath = realpath($currentDir . '/../site/' );
            $absolutePath .= '/' . $fileName;

            $type = new \stdClass();
            $type->id = 700;
            $type->name = "mock";
            $types = array( $type );
            $body = new \stdClass();

            $body->types = $types;

            $output = \json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($absolutePath, $output);
        };

        $tearDownFunction = function() {
            $fileName = "mock.json";
            $currentDir = dirname(__FILE__);
            $absolutePath = realpath($currentDir . '/../site/' );
            $absolutePath .= '/' . $fileName;

            unlink($absolutePath);
        };

        $pactVerifier->ProviderState("A GET request for a setup", $setUpFunction, $tearDownFunction);
    }         
}
```

### Provider Test Filtering
If you want to filter down the interaction you want to test, pass in options to `Verify()`.  Try the below.

```php
<?php

// declare your state
$testState = "There is something to POST to";

// Pick your PSR client.  Guzzle should work as well.
$httpClient = new \Windwalker\Http\HttpClient();

$pactVerifier->ProviderState("Test State")
    ->ServiceProvider("MockApiProvider", $httpClient)
    ->HonoursPactWith("MockApiConsumer")
    ->PactUri($json)
    ->Verify(null, $testState);
  
```

## Project Tests
To run Pact-Php-Native tests, there are several phpunit.xml files.   The provider tests use a Windows method to shutdown the mock server.
Root is expected to be the root of Pact Php Native

- All tests: `php .\vendor\phpunit\phpunit\phpunit -c .\phpunit-all-tests.xml`
- Provider Example: `php .\vendor\phpunit\phpunit\phpunit -c .\phpunit-provider-test.xml`
- Consumer Example: `php .\vendor\phpunit\phpunit\phpunit -c .\phpunit-consumer-test.xml`

## Related Projects
- [Pact.Net](https://github.com/SEEK-Jobs/pact-net)
- [Pact-PHP](https://github.com/andykelk/pact-php)
    - Non-native implementation  
- [Pact-Mock-Service](https://github.com/pact-foundation/pact-mock_service)
    - Nice Ruby layer 