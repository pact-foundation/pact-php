# Basic Consumer Usage

All of the following code will be used exclusively for the Consumer.

## Create Consumer Unit Test

Create a standard PHPUnit test case class and function.

[Click here](../example/json/consumer/tests/Service/ConsumerServiceHelloTest.php) to see the full sample file.

## Create Mock Request

This will define what the expected request coming from your http service will look like.

```php
$request = new ConsumerRequest();
$request
    ->setMethod('GET')
    ->setPath('/hello/Bob')
    ->addHeader('Content-Type', 'application/json');
```

You can also create a body just like you will see in the provider example.

## Create Mock Response

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
dateISO8601 | Regex match a date using the ISO8601 format. | Value (Defaults to 2010-01-01) | $matcher->dateISO8601('2010-01-01')
timeISO8601 | Regex match a time using the ISO8601 format. | Value (Defaults to T22:44:30.652Z) | $matcher->timeISO8601('T22:44:30.652Z')
dateTimeISO8601 | Regex match a datetime using the ISO8601 format. | Value (Defaults to 2015-08-06T16:53:10+01:00) | $matcher->dateTimeISO8601('2015-08-06T16:53:10+01:00')
dateTimeWithMillisISO8601 | Regex match a datetime with millis using the ISO8601 format. | Value (Defaults to 2015-08-06T16:53:10.123+01:00) | $matcher->dateTimeWithMillisISO8601('2015-08-06T16:53:10.123+01:00')
timestampRFC3339 | Regex match a timestamp using the RFC3339 format. | Value (Defaults to Mon, 31 Oct 2016 15:21:41 -0400) | $matcher->timestampRFC3339('Mon, 31 Oct 2016 15:21:41 -0400')
like | Match a value against its data type. | Value | $matcher->like(12)
somethingLike | Alias to like matcher. | Value | $matcher->somethingLike(12)
eachLike | Match on an object like the example. | Value, Min (Defaults to 1) | $matcher->eachLike(12)
constrainedArrayLike | Behaves like the `eachLike` matcher, but also applies a minimum and maximum length validation on the length of the array. The optional `count` parameter controls the number of examples generated. | Value, Min, Max, count (Defaults to null) | $matcher->constrainedArrayLike('test', 1, 5, 3)
boolean | Match against boolean true. | none | $matcher->boolean()
integer | Match a value against integer. | Value (Defaults to 13) | $matcher->integer()
decimal | Match a value against float. | Value (Defaults to 13.01) | $matcher->decimal()
hexadecimal | Regex to match a hexadecimal number. Example: 3F | Value (Defaults to 3F) | $matcher->hexadecimal('FF')
uuid | Regex to match a uuid. | Value (Defaults to ce118b6e-d8e1-11e7-9296-cec278b6b50a) | $matcher->uuid('ce118b6e-d8e1-11e7-9296-cec278b6b50a')
ipv4Address | Regex to match a ipv4 address. | Value (Defaults to 127.0.0.13) | $matcher->ipv4Address('127.0.0.1')
ipv6Address | Regex to match a ipv6 address. | Value (Defaults to ::ffff:192.0.2.128) | $matcher->ipv6Address('::ffff:192.0.2.1')
email | Regex to match an address. | Value (hello@pact.io) | $matcher->email('hello@pact.io')

## Build the Interaction

Now that we have the request and response, we need to build the interaction and ship it over to the mock server.

```php
// Create a configuration that reflects the server that was started. You can
// create a custom MockServerConfigInterface if needed. This configuration
// is the same that is used via the PactTestListener and uses environment variables.
$config  = new MockServerEnvConfig();
$builder = new InteractionBuilder($config);
$builder
    ->given('a person exists', ['name' => 'Bob'])
    ->uponReceiving('a get request to /hello/{name}')
    ->with($request)
    ->willRespondWith($response); // This has to be last. This is what makes FFI calls to register the interaction and start the mock server.
```

### Multiple Interactions

There might be cases where we need multiple interactions with the mock server to have a useful test. For that case we have to:
1. Set the second parameter (`$startMockServer`) of `willRespondWith()` to `false` for every interaction, except the last one. Otherwise, the mock-server will be started already on the first interaction and there will be no way to register more interactions.
2. Run `newInteraction()` on every interaction, except the first one.

   Example:
   ```php
   $builder = new InteractionBuilder($config);
   $builder
       ->uponReceiving('a get request to /hello/{name}')
       ->with($firstRequest)
       ->willRespondWith($firstResponse, false); // set $startMockServer to 'false'
   $builder->newInteraction(); // create a new interaction
   $builder
       ->uponReceiving('a get request to /goodbye/{name}')
       ->with($secondRequest)
       ->willRespondWith($secondResponse); // this will start the mock server
   ```

[Click here](../example/json/consumer/tests/Service/ConsumerServiceMultipleInteractionsTest.php) to see full sample file for multiple interactions.

## Make the Request

```php
$service = new HttpClientService($config->getBaseUri()); // Pass in the URL to the Mock Server.
$result  = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.
```

## Verify Interactions

Verify that all interactions took place that were registered.
This typically should be in each test, that way the test that failed to verify is marked correctly.

```php
$verifyResult = $builder->verify();
$this->assertTrue($verifyResult);
```

## Make Assertions

Verify that the data you would expect given the response configured is correct.

```php
$this->assertEquals('Hello, Bob', $result); // Make your assertions.
```

## Delete Old Pact

If the value of `PACT_FILE_WRITE_MODE` is `merge`, before running the test, we need to delete the old pact manually:

```shell
rm /path/to/pacts/consumer-provider.json
```

## Publish Contracts To Pact Broker

When all tests in test suite are passed, you may want to publish generated contract files to pact broker.

### CLI

Run this command using CLI tool:

```shell
pact-broker publish /path/to/pacts/consumer-provider.json --consumer-app-version 1.0.0 --branch main --broker-base-url https://test.pactflow.io --broker-token SomeToken
```

See more at https://docs.pact.io/pact_broker/publishing_and_retrieving_pacts#publish-using-cli-tools

### Github Actions

See how to use at https://github.com/pactflow/actions/tree/main/publish-pact-files
