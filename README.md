# Pact PHP

[![AppVeyor](https://img.shields.io/appveyor/ci/mattermack/pact-php.svg?style=flat-square&logo=appveyor)](https://ci.appveyor.com/project/mattermack/pact-php/branch/master)
[![Travis](https://img.shields.io/travis/pact-foundation/pact-php.svg?style=flat-square&logo=travis)](https://travis-ci.org/pact-foundation/pact-php)
[![Packagist](https://img.shields.io/packagist/v/mattersight/phppact.svg?style=flat-square&label=stable)](https://packagist.org/packages/mattersight/phppact)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/mattersight/phppact.svg?style=flat-square&label=unstable)](https://packagist.org/packages/mattersight/phppact)

[![Downloads](https://img.shields.io/packagist/dt/mattersight/phppact.svg?style=flat-square)](https://packagist.org/packages/mattersight/phppact)
[![Downloads This Month](https://img.shields.io/packagist/dm/mattersight/phppact.svg?style=flat-square)](https://packagist.org/packages/mattersight/phppact)
[![Downloads Today](https://img.shields.io/packagist/dd/mattersight/phppact.svg?style=flat-square)](https://packagist.org/packages/mattersight/phppact)

PHP version of [Pact](https://pact.io). Enables consumer driven contract testing. Please read the [Pact.io](https://pact.io) for specific information about PACT.

Table of contents
=================

* [Versions](#versions)
* [Specifications](#specifications)
* [Installation](#installation)
* [Basic Consumer Usage](#basic-consumer-usage)
    * [Start and Stop the Mock Server](#start-and-stop-the-mock-server)
    * [Create Consumer Unit Test](#create-consumer-unit-test)
    * [Create Mock Request](#create-mock-request)
    * [Create Mock Response](#create-mock-response)  
    * [Build the Interaction](#build-the-interaction)
    * [Make the Request](#make-the-request)
    * [Make Assertions](#make-assertions)
* [Basic Provider Usage](#basic-provider-usage)
    * [Create Unit Tests](#create-unit-tests)
    * [Start API](#start-api)
    * [Provider Verification](#provider-verification)
        * [Verify From Pact Broker](#verify-from-pact-broker)
        * [Verify All from Pact Broker](#verify-all-from-pact-broker)
        * [Verify Files by Path](#verify-files-by-path)
* [Tips](#tips)
    * [Starting API Asynchronously](#starting-api-asynchronously)
    * [Set Up Provider State](#set-up-provider-state)
    * [Examples](#additional-examples)
        

## Versions

Master and the 3.X tags are a major breaking change to the 2.X versions.   To be similar to the rest of the Pact ecosystem, Pact-PHP migrated to leverage the Ruby backend.  This mirrors the .Net, JS, Python, and Go implementations. 

If you wish to stick with the 2.X implementation, you can continue to pull from the [latest 2.X.X tag](https://github.com/pact-foundation/pact-php/tree/2.2.1). 

## Specifications

The 3.X version is the version of Pact-PHP, not the pact specification version that it supports.   Pact-Php 3.X supports [Pact-Specification 2.X](https://github.com/pact-foundation/pact-specification/tree/version-2).
		
## Installation

Install the latest version with:

```bash
$ composer require mattersight/phppact --dev
```

## Basic Consumer Usage

All of the following code will be used exclusively for the Consumer.

### Start and Stop the Mock Server

This library contains a wrapper for the [Ruby Standalone Mock Service](https://github.com/pact-foundation/pact-mock_service).

The easiest way to configure this is to use a [PHPUnit Listener](https://phpunit.de/manual/current/en/appendixes.configuration.html#appendixes.configuration.test-listeners). A default listener is included in this project, see [PactTestListener.php](/src/PhpPact/Consumer/Listener/PactTestListener.php). This utilizes environmental variables for configurations. These env variables can either be added to the system or to the phpunit.xml configuration file. Here is an example [phpunit.xml](/example/phpunit.consumer.xml) file configured to use the default. Keep in mind that both the test suite and the arguments array must be the same value.

Alternatively, you can start and stop as in whatever means you would like by following this example:

```php
<?php
    use PhpPact\Standalone\MockService\MockServer;
    use PhpPact\Standalone\MockService\MockServerConfig;

    // Create your basic configuration. The host and port will need to match
    // whatever your Http Service will be using to access the providers data.
    $config = new MockServerConfig('localhost', 7200, 'SomeConsumer', 'SomeProvider');
    
    // Instantiate the mock server object with the config. This can be any
    // instance of MockServerConfigInterface.
    $server = new MockServer($this->mockServerConfig);
    
    // Create the process.
    $server->start();
    
    // Stop the process.
    $server->stop();
```

### Create Consumer Unit Test

Create a standard PHPUnit test case class and function.

[Click here](/example/tests/Consumer/Service/ConsumerServiceHelloTest.php) to see the full sample file.

### Create Mock Request

This will define what the expected request coming from your http service will look like.

```php
$request = new ConsumerRequest();
$request
    ->setMethod('GET')
    ->setPath('/hello/Bob')
    ->addHeader('Content-Type', 'application/json');
```

You can also create a body just like you will see in the provider example.

### Create Mock Response

This will define what the response from the provider should look like.

```php
$matcher = new Matcher();

$response = new ProviderResponse();
$response
    ->setStatus(200)
    ->addHeader('Content-Type', 'application/json')
    ->setBody([
        'message' => $matcher->regex('Hello, Bob', '(Hello, )[A-Za-z]')
    ]);
```

In this example, we are using matchers. This allows us to add flexible rules when matching the expectation with the actual value. In the example, you will see regex is used to validate that the response is valid.

```php
$matcher = new Matcher();

$response = new ProviderResponse();
$response
    ->setStatus(200)
    ->addHeader('Content-Type', 'application/json')
    ->setBody([
        'list' => $matcher->eachLike([
            'firstName' => 'Bob',
            'age' => 22
        ])
    ]);
```

Matcher | Explanation | Parameters | Example
---|---|---|---
term | Match a value against a regex pattern. | Value, Regex Pattern | $matcher->term('Hello, Bob', '(Hello, )[A-Za-z]')
regex | Alias to term matcher. | Value, Regex Pattern | $matcher->regex('Hello, Bob', '(Hello, )[A-Za-z]')
dateISO8601 | Regex match a date using the ISO8601 format. Example: 2010-01-01 | Value | $matcher->dateISO8601('2010-01-01')
like | Match a value against its data type. | Value | $matcher->like(12)
eachLike | Match on an object like the example. | Value, Min (Defaults to 1) | $matcher->eachLike(12)

### Build the Interaction

Now that we have the request and response, we need to build the interaction and ship it over to the mock server.

```php
// Create a configuration that reflects the server that was started. You can 
// create a custom MockServerConfigInterface if needed. This configuration
// is the same that is used via the PactTestListener and uses environment variables.
$config  = new MockServerEnvConfig();
$builder = new InteractionBuilder($config);
$builder
    ->given('Get Hello')
    ->uponReceiving('A get request to /hello/{name}')
    ->with($request)
    ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.
```

### Make the Request

```php
$service = new HttpClientService($config->getBaseUri()); // Pass in the URL to the Mock Server.
$result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.
```

### Verify Interactions

Verify that all interactions took place that were registered.
This typically should be in each test, that way the test that failed to verify is marked correctly.

```php
$builder->verify();
```

### Make Assertions

Verify that the data you would expect given the response configured is correct.

```php
$this->assertEquals('Hello, Bob', $result); // Make your assertions.
```

## Basic Provider Usage

All of the following code will be used exclusively for Providers. This will run the Pacts against the real Provider and either verify or fail validation on the Pact Broker.

##### Create Unit Test

Create a single unit test function. This will test a single consumer of the service.

##### Start API

Get an instance of the API up and running. [Click here](#starting-api-asynchronously) for some tips.

If you need to set up the state of your API before making each request please see [Set Up Provider State](#set-up-provider-state).

### Provider Verification

There are three ways to verify Pact files. See the examples below.

##### Verify From Pact Broker

This will grab the Pact file from a Pact Broker and run the data against the stood up API.

```php
$config = new VerifierConfig();
$config
    ->setProviderName('SomeProvider') // Providers name to fetch.
    ->setProviderVersion('1.0.0') // Providers version.
    ->setProviderBaseUrl(new Uri('http://localhost:58000')) // URL of the Provider.
    ->setBrokerUri(new Uri('http://localhost')) // URL of the Pact Broker to publish results.
    ->setPublishResults(true); // Flag the verifier service to publish the results to the Pact Broker.

// Verify that the Consumer 'SomeConsumer' that is tagged with 'master' is valid.
$verifier = new Verifier($config);
$verifier->verify('SomeConsumer', 'master'); // The tag is option. If no tag is set it will just grab the latest.

// This will not be reached if the PACT verifier throws an error, otherwise it was successful.
$this->assertTrue(true, 'Pact Verification has failed.');
```

##### Verify All from Pact Broker

This will grab every Pact file associated with the given provider.

```php
public function testPactVerifyAll()
{
    $config = new VerifierConfig();
    $config
        ->setProviderName('SomeProvider') // Providers name to fetch.
        ->setProviderVersion('1.0.0') // Providers version.
        ->setProviderBaseUrl(new Uri('http://localhost:58000')) // URL of the Provider.
        ->setBrokerUri(new Uri('http://localhost')) // URL of the Pact Broker to publish results.
        ->setPublishResults(true); // Flag the verifier service to publish the results to the Pact Broker.

    // Verify that all consumers of 'SomeProvider' are valid.
    $verifier = new Verifier($config);
    $verifier->verifyAll();

    // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
    $this->assertTrue(true, 'Pact Verification has failed.');
}
```

##### Verify Files by Path

This allows local Pact file testing.

```php
public function testPactVerifyAll()
{
    $config = new VerifierConfig();
    $config
        ->setProviderName('SomeProvider') // Providers name to fetch.
        ->setProviderVersion('1.0.0') // Providers version.
        ->setProviderBaseUrl(new Uri('http://localhost:58000')) // URL of the Provider.
        ->setBrokerUri(new Uri('http://localhost')) // URL of the Pact Broker to publish results.
        ->setPublishResults(true); // Flag the verifier service to publish the results to the Pact Broker.

    // Verify that the files in the array are valid.
    $verifier = new Verifier($config);
    $verifier->verifyFiles(['C:\SomePath\consumer-provider.json']);

    // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
    $this->assertTrue(true, 'Pact Verification has failed.');
}
```

## Tips

### Starting API Asynchronously

You can use the built in PHP server to accomplish this during your tests setUp function. The Symfony Process library can be used to run the process asynchronous.

[PHP Server](http://php.net/manual/en/features.commandline.webserver.php)

[Symfony Process](https://symfony.com/doc/current/components/process.html)

### Set Up Provider State

The PACT verifier is a wrapper of the [Ruby Standalone Verifier](https://github.com/pact-foundation/pact-provider-verifier).
See [API with Provider States](https://github.com/pact-foundation/pact-provider-verifier#api-with-provider-states) for more information on how this works.
Since most PHP rest APIs are stateless, this required some thought.

Here are some options:
1. Write the posted state to a file and use a factory to decide which mock repository class to use based on the state.
2. Set up your database to meet the expectations of the request. At the start of each request, you should first reset the database to its original state.

No matter which direction you go, you will have to modify something outside of the PHP process because each request to your server will be stateless and independent.

### Additional Examples
There is a separate repository with an end to end example for both the 2.X and 3.X implementations.   
- [pact-php-example](https://github.com/mattermack/pact-php-example) for 3.X examples
- [2.2.1 tag](https://github.com/mattermack/pact-php-example/tree/2.2.1) for 2.X examples
