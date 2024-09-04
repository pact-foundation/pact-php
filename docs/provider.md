# Basic Provider Usage

All of the following code will be used exclusively for Providers. This will run the Pacts against the real Provider and either verify or fail validation on the Pact Broker.

## Create Unit Test

Create a single unit test function. This will test all defined consumers of the service.

```php
protected function setUp(): void
{
    // Start API
}

protected function tearDown(): void
{
    // Stop API
}

public function testPactVerifyConsumers(): void
{
    $config = new VerifierConfig();
    $config->getProviderInfo()
        ->setName('someProvider')
        ->setHost('localhost')
        ->setPort(8000);
    $config->getProviderState()
        ->setStateChangeUrl(new Uri('http://localhost:8000/pact-change-state'))
        ->setStateChangeTeardown(true)
        ->setStateChangeAsBody(true);

    // If your provider dispatch messages
    $config->addProviderTransport(
        (new ProviderTransport())
            ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
            ->setPort(8000)
            ->setPath('/pact-messages')
            ->setScheme('http')
    );

    // If you want to publish verification results to Pact Broker.
    if ($isCi = getenv('CI')) {
        $publishOptions = new PublishOptions();
        $publishOptions
            ->setProviderVersion(exec('git rev-parse --short HEAD'))
            ->setProviderBranch(exec('git rev-parse --abbrev-ref HEAD'));
        $config->setPublishOptions($publishOptions);
    }

    // If you want to display more/less verification logs.
    if ($logLevel = \getenv('PACT_LOGLEVEL')) {
        $config->setLogLevel($logLevel);
    }

    // Add sources ...

    $verifyResult = $verifier->verify();

    $this->assertTrue($verifyResult);
}
```

## Add Custom Headers Prior to Verification

Sometimes you may need to add custom headers to the requests that can't be persisted in a pact file.
e.g. an OAuth bearer token: `Authorization: Bearer 1a2b3c4d5e6f7g8h9i0k`

```php
$config->getCustomHeaders()
    ->addHeader('Authorization', 'Bearer 1a2b3c4d5e6f7g8h9i0k');
```

The requests will have custom headers added before being sent to the Provider API.

> Note: Custom headers are not the only approach for authentication and authorization. For other approaches, please refer to this [documentation](https://docs.pact.io/provider/handling_auth#4-modify-the-request-to-use-real-credentials).

> **Important Note:** You should only use this feature for headers that can not be persisted in the pact file. By modifying the request, you are potentially modifying the contract from the consumer tests!

## Verification Sources

There are four ways to verify Pact files. See the examples below.

### Verify From Pact Broker

This will grab the Pact files from a Pact Broker.

```php
$selectors = (new ConsumerVersionSelectors())
    ->addSelector(new Selector(mainBranch: true))
    ->addSelector(new Selector(deployedOrReleased: true));

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

$verifier->addBroker($broker);
```

### Verify From Url

This will grab the Pact file from a url.

```php
$url = new Url();
$url
    ->setUrl(new Uri('http://localhost:9292/pacts/provider/personProvider/consumer/personConsumer/latest'))
    ->setUsername('user')
    ->setPassword('pass')
    ->setToken('token');

$verifier->addUrl($url);
```

### Verify Files in Directory

This will grab local Pact files in directory. Results will not be published.

```php
$verifier->addDirectory('C:\SomePath');
```

### Verify Files by Path

This will grab local Pact file. Results will not be published.

```php
$verifier->addFile('C:\SomePath\consumer-provider.json');
```

## Start API

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
