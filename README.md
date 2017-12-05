# Pact PHP

[![Build status](https://ci.appveyor.com/api/projects/status/o18awsc0chcw184d/branch/master?svg=true)](https://ci.appveyor.com/project/mattermack/pact-php/branch/master)

![Pact-PHP](https://raw.githubusercontent.com/pact-foundation/pact-php/master/pact-php.png)

PHP version of Pact. Enables consumer driven contract testing, providing a mock service and DSL for the consumer project, and interaction playback and verification for the service provider project.

This is a project to provide Pact functionality completely in PHP. This started as a straight port of 
[Pact.Net](https://github.com/SEEK-Jobs/pact-net) on the 1.1 specification. Overtime, the project adjusted to a more 
PHP way of doing things.  This project now supports the 2.0 pact specification and associated tests. 

The namespace is PhpPact as [Pact-PHP](https://github.com/andykelk/pact-php) uses the namespace of PactPhp.


## Composer
If you want to run this on Windows, because of dependencies in PHP Unit and prettier output, certain libraries had to be included.
Thus, there are two ways to run composer update on Windows
 1. `composer update --ignore-platform-reqs`
 2. `composer update --no-dev`

### Pact-PHP 2.0
For Pact-PHP 2.0, there is a need to run min-stability dev and pull from a feature addition to [Peekmo/jsonpath](https://github.com/Peekmo/JsonPath), you will need to use the following composer.json
```json
{
	"prefer-stable": true,
	"minimum-stability": "dev",
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/mattermack/JsonPath"
		}
	],
	"require":
	{
		"mattersight/phppact": "^2.0"
	}
}
```

## PHP Extentions
To support XML, you need the `php_xsl` extension.
  
To support PactBrokerConnector, you need `php_curl` extension and possibly `php_openssl`
 

## Pull Requests
This project is actively taking pull requests and appreciate the contribution.   The code needs to pass the CI validation 
which can be found at [AppVeyor](https://ci.appveyor.com/project/mattermack/pact-php)

All code needs to pass a PSR-2 lint check, which is wrapped into a Powerscript to download php-cs-fixer-v2.phar and run 
the lint checker against the same command as the CI tool.

To have the lint checker auto correct your code, run locally using the Powershell command: `.\linter.ps1 -fix $true`

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
    public function getBasic($url)
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
        $request = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest(\PhpPact\Mocks\MockHttpService\Models\HttpVerb::GET, "/", $reqHeaders);

        // build the response
        $resHeaders = array();
        $resHeaders["Content-Type"] = "application/json";
        $resHeaders["AnotherHeader"] = "my-header";

        $response = new \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse('200', $resHeaders);
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
            $results = $mockService->verifyInteractions();

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

$pactVerifier->providerState("A GET request to get types")
                ->serviceProvider("MockApiProvider", $httpClient)
                ->honoursPactWith("MockApiConsumer")
                ->pactUri('../pact/mockapiconsumer-mockapiprovider.json')
                ->verify();
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
        
        // whatever your URL of choice is
        $uri = WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;

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

        // wherever your PACT file is
        // you may want to leverage PactBrokerConnector to pull this
        $json = 'mockapiconsumer-mockapiprovider.json';

        $pactVerifier->providerState("A GET request for a setup", $setUpFunction, $tearDownFunction)
            ->serviceProvider("MockApiProvider", $httpClient)
            ->honoursPactWith("MockApiConsumer")
            ->pactUri($json)
            ->verify(); // note that this should test all as we can run setup and tear down
            
        
        
    }         
}
```

### Provider Test Filtering
If you want to filter down the interaction you want to test, pass in options to `verify()`.  Try the below.

```php
<?php

// declare your state
$testState = "There is something to POST to";

// Pick your PSR client.  Guzzle should work as well.
$httpClient = new \Windwalker\Http\HttpClient();

$pactVerifier->providerState("Test State")
    ->serviceProvider("MockApiProvider", $httpClient)
    ->honoursPactWith("MockApiConsumer")
    ->pactUri($json)
    ->verify(null, $testState);
  
```

### Matchers
This is a PHP specific implementation of Matchers.  This was derived from the [JVM Matching Instructions.](https://github.com/DiUS/pact-jvm/wiki/Matching)   
While all [Pact Specifications under v2](https://github.com/pact-foundation/pact-specification/tree/version-2) are implemented and passing, I do not 
believe the spirit of some of the cases are not honored.   Refactoring will certainly need to be done in some cases.

All matchers need to be defined by a JSONPath and attached to either the Request or Response object.  There are all kinds of gotchas,
which have been documented on [Matching Gotchas](https://github.com/realestate-com-au/pact/wiki/Matching-gotchas)

For PHP gotchas, the Pact-PHP added the first and last backslash ( / ).   For example, if you wanted to have a regex for just words instead of 
`/\w+/`, you would just put in `\w+`.  

Responser body matchers need to follow Postel's law.   Below there are two matchers:
1. Confirm that all responses have the same type
2. Confirm `walrus` is in the response body
```php
<?php 
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
```

## Pact Broker Integration
To integrate with your pact broker host, there are several options. This section focuses on the ```PactBrokerConnector```.  To be fair, the pact broker authentication is currently untested but mirrors the implementation in pact-js.
 
### Publishing Pacts
There are several hopefully self explanatory functions in ```PactBrokerConnector```:

- publishFile - reads the JSON from a file
- publishJson - publishes from a JSON string
- publish - publishes from a ```ProviderServicePactFile``` object

```php
<?php

// create your options
$uriOptions = new \PhpPact\PactUriOptions("http://your-pact-broker" );
$connector = new \PhpPact\PactBrokerConnector($uriOptions);

// Use the appropriate function to read from a file, JSON string, or ProviderServicePactFile object
$file = __DIR__ . '/../example/pact/mockapiconsumer-mockapiprovider.json';
$statusCode = $connector->publishFile($file, '1.0.3');

```

### Retrieving Pacts
If you have an open pact broker, ```$pactVerifier->PactUri``` uses ```file_get_contents``` which accepts a URL.  You could simply use this technique in those cases.

To do some more robust interactions, There are several hopefully self explanatory functions in ```PactBrokerConnector```:

- retrieveLatestProviderPacts - retrieve all the latest pacts associated with this provider
- retrievePact - retrieve particular pact

```php
<?php

// create your options
$uriOptions = new \PhpPact\PactUriOptions("http://your-pact-broker" );
$connector = new \PhpPact\PactBrokerConnector($uriOptions);

// particular version
$pact = $connector->retrievePact("MockApiProvider", "MockApiConsumer", "1.0.2");
error_log(\json_encode($pact,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// get all pacts for this provider
$pacts = $connector->retrieveLatestProviderPacts("MockApiProvider");
$pact = array_pop($pacts);
error_log(\json_encode($pact,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

```

### Publishing verification results to Broker
If the Provider is PHP, a simple wrapper exists in the connector to publish the verification back to the broker. 
The complexities lies in passing the build version, build url, and pact version back.   Your CI tools should be able to provider this data.

```php
<?php

$uriOptions = new \PhpPact\PactUriOptions("http://your-pact-broker" );
$connector = new \PhpPact\PactBrokerConnector($uriOptions);

$connector->verify("MockProvider", "MockConsumer", "cd3e2a61063e428b5bd4e91c5e6c5dee0c45cf99", true, '0.0.42', "http://your-ci-builder/api/pact-example-api/job/master/42/");

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