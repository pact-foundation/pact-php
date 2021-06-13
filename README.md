# Pact PHP

![pact-php](https://github.com/pact-foundation/pact-php/workflows/pact-php/badge.svg)
[![Packagist](https://img.shields.io/packagist/v/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)

[![Downloads](https://img.shields.io/packagist/dt/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)
[![Downloads This Month](https://img.shields.io/packagist/dm/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)

PHP version of [Pact](https://pact.io). Enables consumer driven contract testing. Please read the [Pact.io](https://pact.io) for specific information about PACT.

Table of contents
=================

* [Versions](#versions)
* [Specifications](#specifications)
* [Installation](#installation)
* [Basic Consumer Usage](#basic-consumer-usage)
    * [Configure PHPUnit](#configure-phpunit)
    * [Create Mock Server Config](#create-mock-server-config)
    * [Create Consumer Unit Test](#create-consumer-unit-test)
    * [Create Mock Request](#create-mock-request)
    * [Create Mock Response](#create-mock-response)
    * [Build the Interaction](#build-the-interaction)
    * [Make the Request](#make-the-request)
    * [Make Assertions](#make-assertions)
* [Basic Provider Usage](#basic-provider-usage)
    * [Create Unit Tests](#create-unit-test)
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
8.x adds support for pact specification 3.X & 4.X via Pact FFI.  This results in dropping support for PHP 7.3.
7.x updates internal dependencies and libraries, mostly to Guzzle 7.X.  This results in dropping support for PHP 7.2.
6.x updates internal dependencies, mostly surrounding the Amp library.  This results in dropping support for PHP 7.1.

5.X adds preliminary support for async messages and pact specification 3.X.  This does not yet support the full pact specification 3.X as the backend implementations are incomplete. However, pact-messages are supported.

The 4.X tags are accompany changes in PHPUnit 7.X which requires a PHP 7.1 or higher.  Thus, 4.X drops support for PHP 7.0.

The 3.X tags are a major breaking change to the 2.X versions.   To be similar to the rest of the Pact ecosystem, Pact-PHP migrated to leverage the Ruby backend.  This mirrors the .Net, JS, Python, and Go implementations.

If you wish to stick with the 2.X implementation, you can continue to pull from the [latest 2.X.X tag](https://github.com/pact-foundation/pact-php/tree/2.2.1).

## Specifications

The 3.X version is the version of Pact-PHP, not the pact specification version that it supports.

Pact-Php 3.X supports [Pact-Specification 2.X](https://github.com/pact-foundation/pact-specification/tree/version-2).
Pact-Php 8.X supports:
    * [Pact-Specification 3.X](https://github.com/pact-foundation/pact-specification/tree/version-3).
    * [Pact-Specification 4.X](https://github.com/pact-foundation/pact-specification/tree/version-4).

## Installation

Install the latest version with:

```bash
$ composer require pact-foundation/pact-php --dev
```

Composer hosts older versions under `mattersight/phppact`, which is abandoned. Please convert to the new package name.

## Basic Consumer Usage

All of the following code will be used exclusively for the Consumer.

### Configure PHPUnit

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="../vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="PhpPact Example Tests">
            <directory>./tests/Consumer</directory>
        </testsuite>
    </testsuites>
    <listeners>
        <listener class="PhpPact\Consumer\Listener\PactTestListener">
            <arguments>
                <array>
                    <element key="0">
                        <string>PhpPact Example Tests</string>
                    </element>
                </array>
            </arguments>
        </listener>
    </listeners>
    <php>
        <env name="PACT_CONSUMER_NAME" value="someConsumer"/>
        <env name="PACT_CONSUMER_VERSION" value="1.0.0"/>
        <env name="PACT_CONSUMER_TAG" value="master"/>
        <env name="PACT_OUTPUT_DIR" value=".\example\output"/>
        <!-- <env name="PACT_BROKER_URI" value="https://test.pact.dius.com.au"/> -->
        <!-- <env name="PACT_BROKER_BEARER_TOKEN" value="token"/> -->
    </php>
</phpunit>
```

### Create Mock Server Config

Create mock server config from environment variables:

```php
use PhpPact\Standalone\MockService\MockServerEnvConfig;

$config  = new MockServerEnvConfig();
$config->setProvider('someProvider');
```

Or create it manually:

```php
use PhpPact\Standalone\MockService\MockServerConfig;

$config = new MockServerConfig();
$config->setConsumer('someConsumer');
$config->setProvider('someProvider');
$config->setPactDir('.\example\output');
$config->setPactSpecificationVersion('3.0.0');
```

### Create Consumer Unit Test

Create a standard PHPUnit test case class and function.

[Click here](/example/tests/Consumer/Service/ConsumerServiceHelloTest.php) to see the full sample file.

### Create Mock Request

This will define what the expected request coming from your http service will look like.

```php
use PhpPact\Consumer\Model\ConsumerRequest;

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
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ProviderResponse;

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
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ProviderResponse;

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
dateISO8601 | Regex match a date using the ISO8601 format. | Value (Defaults to 2010-01-01) | $matcher->dateISO8601('2010-01-01')
timeISO8601 | Regex match a time using the ISO8601 format. | Value (Defaults to T22:44:30.652Z) | $matcher->timeISO8601('T22:44:30.652Z')
dateTimeISO8601 | Regex match a datetime using the ISO8601 format. | Value (Defaults to 2015-08-06T16:53:10+01:00) | $matcher->dateTimeISO8601('2015-08-06T16:53:10+01:00')
dateTimeWithMillisISO8601 | Regex match a datetime with millis using the ISO8601 format. | Value (Defaults to 2015-08-06T16:53:10.123+01:00) | $matcher->dateTimeWithMillisISO8601('2015-08-06T16:53:10.123+01:00')
timestampRFC3339 | Regex match a timestamp using the RFC3339 format. | Value (Defaults to Mon, 31 Oct 2016 15:21:41 -0400) | $matcher->timestampRFC3339('Mon, 31 Oct 2016 15:21:41 -0400')
like | Match a value against its data type. | Value | $matcher->like(12)
somethingLike | Alias to like matcher. | Value | $matcher->somethingLike(12)
eachLike | Match on an object like the example. | Value, Min (Defaults to 1) | $matcher->eachLike(12)
boolean | Match against boolean true. | none | $matcher->boolean()
integer | Match a value against integer. | Value (Defaults to 13) | $matcher->integer()
decimal | Match a value against float. | Value (Defaults to 13.01) | $matcher->decimal()
hexadecimal | Regex to match a hexadecimal number. Example: 3F | Value (Defaults to 3F) | $matcher->hexadecimal('FF')
uuid | Regex to match a uuid. | Value (Defaults to ce118b6e-d8e1-11e7-9296-cec278b6b50a) | $matcher->uuid('ce118b6e-d8e1-11e7-9296-cec278b6b50a')
ipv4Address | Regex to match a ipv4 address. | Value (Defaults to 127.0.0.13) | $matcher->ipv4Address('127.0.0.1')
ipv6Address | Regex to match a ipv6 address. | Value (Defaults to ::ffff:192.0.2.128) | $matcher->ipv6Address('::ffff:192.0.2.1')

### Build the Interaction

Now that we have the request and response, we need to build the interaction and ship it over to the mock server.

```php
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Standalone\MockService\MockServerEnvConfig;

// Create a configuration that reflects the server that was started. You can
// create a custom MockServerConfigInterface if needed. This configuration
// is the same that is used via the PactTestListener and uses environment variables.
$config  = new MockServerEnvConfig();
$builder = new InteractionBuilder($config);
$builder
    ->newInteraction() // Support multiple interactions per mock server. Call to create new interaction.
    ->given('a person exists')
    ->uponReceiving('a get request to /hello/{name}')
    ->with($request)
    ->willRespondWith($response)
    ->createMockServer(); // This has to be last.
```

### Make the Request

```php
$service = new HttpClientService($builder->getBaseUri()); // Pass in the URL to the Mock Server.
$result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.
```

### Verify Interactions

Verify that all interactions took place that were registered.
This typically should be in each test, that way the test that failed to verify is marked correctly.

```php
$this->assertTrue($builder->verify());
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
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;

$selectors = (new ConsumerVersionSelectors())
    ->addSelector('{"tag":"foo","latest":true}')
    ->addSelector('{"tag":"bar","latest":true}');

$config = new VerifierConfig();
$config
    ->setProviderName('someProvider') // Providers name to fetch.
    ->setProviderVersion('1.0.0') // Providers version.
    ->setProviderTags(['prod' ,'dev'])
    ->setProviderBranch('main')
    ->setScheme('http')
    ->setHost('localhost')
    ->setPort(58000)
    ->setBasePath('/')
    ->setStateChangeUrl(new Uri('http://localhost:58000/change-state'))
    ->setBuildUrl(new Uri('http://build.domain.com'))
    ->setFilterConsumerNames('someConsumer', 'otherConsumer')
    ->setFilterDescription('Send POST to create')
    ->setFilterNoState(true)
    ->setFilterState('state')
    ->setPublishResults(true)
    ->setDisableSslVerification(true)
    ->setStateChangeAsQuery(true)
    ->setStateChangeTeardown(true)
    ->setRequestTimeout(500);

$broker = new Broker();
$broker
    ->setUrl(new Uri('http://localhost'))
    ->setUsername('user')
    ->setPassword('pass')
    ->setToken('token')
    ->setEnablePending(true)
    ->setIncludeWipPactSince('2020-01-30')
    ->setProviderTags(['prod'])
    ->setProviderBranch('main')
    ->setConsumerVersionSelectors($selectors)
    ->setConsumerVersionTags(['dev']);

$verifier = new Verifier();
$verifier->newHandle($config);
$verifier->addBroker($broker);

$this->assertTrue($verifier->verify());
```

##### Verify All from Pact Broker

This will grab every Pact file associated with the given provider.

```php
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;

public function testPactVerifyAll()
{
    $broker = new Broker();
    $broker
        ->setUrl(new Uri('http://localhost'))
        ->setUsername('user')
        ->setPassword('pass')
        ->setToken('token');

    /** @var Verifier $verifier */
    $verifier->addBroker($broker);

    $this->assertTrue($verifier->verify());
}
```

##### Verify Files by Path

This allows local Pact file testing.

```php
use PhpPact\Standalone\ProviderVerifier\Verifier;

public function testPactVerifyLocal()
{
    /** @var Verifier $verifier */
    $verifier->addFile('C:\SomePath\consumer-provider.json');

    $this->assertTrue($verifier->verify());
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

## Message support
The goal is not to test the transmission of an object over a bus but instead vet the contents of the message.
While examples included focus on a Rabbit MQ, the exact message queue is irrelevant. Initial comparisons require a certain
object type to be created by the Publisher/Producer and the Consumer of the message.  This includes a metadata set where you
can store the key, queue, exchange, etc that the Publisher and Consumer agree on.  The content format needs to be JSON.

To take advantage of the existing pact-verification tools, the provider side of the equation stands up an http proxy to callback
to processing class.   Aside from changing default ports, this should be transparent to the users of the libary.

Both the provider and consumer side make heavy use of lambda functions.

### Consumer Side Message Processing
The examples provided are pretty basic.   See examples\tests\MessageConsumer.
1. Create the content and metadata (array)
1. Annotate the MessageBuilder appropriate content and states
    1. Given = Provider State
    1. expectsToReceive = Description
1. Set the callback you want to run when a message is provided
    1. The callback must accept a JSON string as a parameter
1. Run Verify.  If nothing blows up, #winning.

```php
use PhpPact\Consumer\MessageBuilder;
use MessageConsumer\ExampleMessageConsumer;

$config  = new MockServerEnvConfig();
$builder = new MessageBuilder($config);

$contents       = new \stdClass();
$contents->song = 'And the wind whispers Mary';

$metadata = ['queue'=>'And the clowns have all gone to bed', 'routing_key'=>'And the clowns have all gone to bed'];

$builder
    ->given('You can hear happiness staggering on down the street')
    ->expectsToReceive('footprints dressed in red')
    ->withMetadata($metadata)
    ->withContent($contents);

// established mechanism to this via callbacks
$consumerMessage = new ExampleMessageConsumer();
$callback        = [$consumerMessage, 'ProcessSong'];
$builder->setCallback($callback);

$this->assertTrue($builder->verify());
```


### Provider Side Message Validation

Create a proxy to handle these requests:

1. POST /change-state
    1. Set up your database to meet the expectations of the request
    1. Reset the database to its original state.
2. POST /
    1. Return message's content in body
    2. Return message's metadata in header `PACT-MESSAGE-METADATA`
3. If nothing matches, proxy the request to provider

[Click here](/example/tests/Provider/public/proxy.php) to see the full sample file.

## Usage for the optional `pact-stub-service`

If you would like to test with fixtures, you can use the `pact-stub-service` like this:

```php
use PhpPact\Standalone\StubService\StubServerConfig;
use PhpPact\Standalone\StubService\StubServer;
use PhpPact\Standalone\StubService\Service\StubServerHttpService;

$pactLocation             = __DIR__ . '/someconsumer-someprovider.json';
$port                     = 7201;
$endpoint                 = 'test';

$config = (new StubServerConfig())
            ->setFiles($pactLocation)
            ->setPort($port)
            ->setEndpoint($endpoint);

$stubServer = new StubServer($config);
$stubServer->start();

$service = new StubServerHttpService(new GuzzleClient(), $config);

echo $service->getJson(); // output: {"results":[{"name":"Games"}]}
```
