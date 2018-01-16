# Pact PHP

[![Build status](https://ci.appveyor.com/api/projects/status/o18awsc0chcw184d/branch/master?svg=true)](https://ci.appveyor.com/project/mattermack/pact-php/branch/master)

![Pact-PHP](https://raw.githubusercontent.com/pact-foundation/pact-php/master/pact-php.png)

PHP version of Pact. Enables consumer driven contract testing, providing a mock service and DSL for the consumer project, and interaction playback and verification for the service provider project.

This is a project to provide Pact functionality completely in PHP. This started as a straight port of 
[Pact.Net](https://github.com/pact-foundation/pact-net) on the 1.1 specification. Overtime, the project
adjusted to a more PHP way of doing things.  This project now supports the 2.0 pact specification and
associated tests.

The project is now evolving into a wrapper around the [Ruby Pact Standalone](https://github.com/pact-foundation/pact-ruby-standalone) project. The consumer Mock Server is complete.

### Pre-requisites
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
	]
}
```

## Install with Composer
`composer require mattersight/phppact --dev`

## Pull Requests
This project is actively taking pull requests and appreciate the contribution.   The code needs to pass the CI validation 
which can be found at [AppVeyor](https://ci.appveyor.com/project/mattermack/pact-php)

A code fixer will run prior to running unit tests in CI. Please run `composer test` before making a PR to verify that it is working.

## Service Consumer

#### 1. Add Pact Listener to PHPUnit Configuration

The default PACT listener requires that you use environment variables to set up the mock server configuration. You can extend or implement another listener if need be.

The listener accomplishes a few necessary functions. It starts the mock server, it publishes the results for you, and it stops the mock server after the test suite is complete.

For this to function properly, you need to create a Test Suite, add a listener section, with an argument of the Test Suite name, and the necessary environment variables.


   ```xml
   <?xml version="1.0" encoding="UTF-8"?>
   <phpunit bootstrap="../vendor/autoload.php">
       <testsuites>
           <testsuite name="PhpPact Example Tests">
               <directory>./tests/Consumer</directory>
           </testsuite>
       </testsuites>
       <listeners>
           <listener class="PhpPact\Consumer\Listener\PactTestListener">
               <arguments>
                   <array>
                       <element>
                           <string>PhpPact Example Tests</string>
                       </element>
                   </array>
               </arguments>
           </listener>
       </listeners>
       <php>
           <env name="PACT_MOCK_SERVER_HOST" value="localhost"/>
           <env name="PACT_MOCK_SERVER_PORT" value="7200"/>
           <env name="PACT_CONSUMER_NAME" value="someConsumer"/>
           <env name="PACT_CONSUMER_VERSION" value="1.0.0"/>
           <env name="PACT_PROVIDER_NAME" value="someProvider"/>
           <env name="PACT_BROKER_URI" value="http://localhost"/>
       </php>
   </phpunit>
   ```

Here is the included listener.

   ```php
    <?php
    
    namespace PhpPact\Consumer\Listener;
    
    use GuzzleHttp\Psr7\Uri;
    use PhpPact\Broker\Service\BrokerHttpService;
    use PhpPact\Http\GuzzleClient;
    use PhpPact\Standalone\Installer\InstallManager;
    use PhpPact\Standalone\MockServer\MockServer;
    use PhpPact\Standalone\MockServer\MockServerConfigInterface;
    use PhpPact\Standalone\MockServer\MockServerEnvConfig;
    use PhpPact\Standalone\MockServer\Service\MockServerHttpService;
    use PHPUnit\Framework\TestListener;
    use PHPUnit\Framework\TestListenerDefaultImplementation;
    use PHPUnit\Framework\TestSuite;
    
    /**
    * PACT listener that can be used with environment variables and easily attached to PHPUnit configuration.
    * Class PactTestListener
    */
    class PactTestListener implements TestListener
    {
    use TestListenerDefaultImplementation;
    
    /** @var MockServer */
    private $server;
    
    /**
     * Name of the test suite configured in your phpunit config.
     *
     * @var string
     */
    private $testSuiteNames;
    
    /** @var MockServerConfigInterface */
    private $mockServerConfig;
    
    /**
     * PactTestListener constructor.
     *
     * @param string[] $testSuiteNames test suite names that need evaluated with the listener
     */
    public function __construct(array $testSuiteNames)
    {
        $this->testSuiteNames   = $testSuiteNames;
        $this->mockServerConfig = new MockServerEnvConfig();
    }
    
    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            $this->server = new MockServer($this->mockServerConfig, new InstallManager());
            $this->server->start();
        }
    }
    
    /**
     * Publish JSON results to PACT Broker and stop the Mock Server.
     *
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
        if (\in_array($suite->getName(), $this->testSuiteNames)) {
            try {
                $httpService = new MockServerHttpService(new GuzzleClient(), $this->mockServerConfig);
                $httpService->verifyInteractions();
    
                $json = $httpService->getPactJson();
            } finally {
                $this->server->stop();
            }
    
            $brokerHttpService = new BrokerHttpService(new GuzzleClient(), new Uri(\getenv('PACT_BROKER_URI')));
            $brokerHttpService->publishJson($json, \getenv('PACT_CONSUMER_VERSION'));
        }
    }
    }
   ```
    
### 2. Write your tests
Create a new test case within your service consumer test project, using whatever test framework you like (in this case we used phpUnit). 
Then implement your tests.

   ```php
    <?php
    
    namespace Consumer\Service;
    
    use PhpPact\Consumer\InteractionBuilder;
    use PhpPact\Consumer\Matcher\RegexMatcher;
    use PhpPact\Consumer\Model\ConsumerRequest;
    use PhpPact\Consumer\Model\ProviderResponse;
    use PhpPact\Standalone\MockServer\MockServerEnvConfig;
    use PHPUnit\Framework\TestCase;
    
    class ConsumerServiceHelloTest extends TestCase
    {
        /**
         * Example PACT test.
         */
        public function testGetHelloString()
        {
            // Create your expected request from the consumer.
            $request = new ConsumerRequest();
            $request
                ->setMethod('GET')
                ->setPath('/hello/Bob')
                ->addHeader('Content-Type', 'application/json');
    
            // Create your expected response from the provider.
            $response = new ProviderResponse();
            $response
                ->setStatus(200)
                ->addHeader('Content-Type', 'application/json')
                ->setBody([
                    'message' => new RegexMatcher('Hello, Bob', '(Hello, )[A-Za-z]') // Use matches directly in the body. These will get parsed into JSON automatically before being sent to the PACT Broker.
                ]);
    
            // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
            $config      = new MockServerEnvConfig();
            $mockService = new InteractionBuilder($config);
            $mockService
                ->given('Get Hello')
                ->uponReceiving('A get request to /hello/{name}')
                ->with($request)
                ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.
    
            $service = new HttpService($config->getBaseUri()); // Pass in the URL to the Mock Server.
            $result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.
    
            $this->assertEquals('Hello, Bob', $result); // Make your assertions.
        }
    }
   ```
   
Right now there are 2 Available Matches. These matches can be used directly in the setBody function.

Matcher | Explanation | Example
---|---|---
PhpPact\Consumer\Matcher\RegexMatcher | Match a value against a regex pattern. | new RegexMatcher('Hello, Bob', '(Hello, )[A-Za-z]')
PhpPact\Consumer\Matcher\RegexMatcher | Match a value against its data type. | new TypeMatch(12)
   

### 3. Run the test
Everything should be green and your PACT file should be published to the broker as expected.


## Service Provider

### 1. Build API
Get an instance of the API up and running.  If your API support PHP's [built-in web server](http://php.net/manual/en/features.commandline.webserver.php), see [this great tutorial](http://tech.vg.no/2013/07/19/using-phps-built-in-web-server-in-your-test-suites/) on 
bootstrapping phpunit to spin up the API, run tests, and tear down the API.   See examples/site/provider.php and 
examples/test/bootstrap.php for a local server on Windows.

### 2. Configure PHP unit
Bootstrap PHPUnit with appropriate composer and autoloaders.   Optionally, add a bootstrap api from the *Build API* section.   

### 3. Tell the provider it needs to honor the pact
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
Everything should be green and the pact file should be published to the broker.

## Examples
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

## Pact Broker Integration
To integrate with your pact broker host, there are several options. This section focuses on the `PactBrokerConnector`.  To be fair, the pact broker authentication is currently untested but mirrors the implementation in pact-js.
 
### Publishing Pacts
The PACT file should be sent automatically using the PactTestListener.

Here is an example of publishing manually.

   ```php
    <?php
    
    use GuzzleHttp\Psr7\Uri;
    use PhpPact\Broker\Service\BrokerHttpService;
    use PhpPact\Http\GuzzleClient;
    
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    $httpService = new BrokerHttpService(new GuzzleClient(), new Uri('http://localhost:80/'));
    
    $json = json_encode([
        "consumer" => "someConsumer",
        "provider" => "someProvider"
    ]);
    
    $httpService->publishJson($json, '1.0.0');
   ```

### Retrieving Pacts
If you have an open pact broker, `$pactVerifier->PactUri` uses `file_get_contents` which accepts a URL.  You could simply use this technique in those cases.

To do some more robust interactions, There are several hopefully self explanatory functions in `PactBrokerConnector`:

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
The complexities lies in passing the build version and build url among other things.   Your CI tools should be able to provider this data.

```php
<?php

$uriOptions = new \PhpPact\PactUriOptions("http://your-pact-broker" );
$connector = new \PhpPact\PactBrokerConnector($uriOptions);
$connector->verify(true, "http://your-ci-builder/api/pact-example-api/job/master/42/", "MockProvider", '0.0.42', 'MockConsumer', 'latest');
```

## Project Tests
To run the projects tests run `composer test`.

## Related Projects
- [Pact.Net](https://github.com/SEEK-Jobs/pact-net)
- [Pact-PHP](https://github.com/andykelk/pact-php)
    - Non-native implementation  
- [Pact-Mock-Service](https://github.com/pact-foundation/pact-mock_service)
    - Nice Ruby layer 