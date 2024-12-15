<span align="center">

![logo](https://user-images.githubusercontent.com/53900/121775784-0191d200-cbcd-11eb-83dd-adc001b94519.png)

# Pact PHP

<!-- Please use absolute URLs for all links as the content of this page is synced to docs.pact.io -->

![Code Analysis & Test](https://github.com/pact-foundation/pact-php/actions/workflows/build.yml/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/pact-foundation/pact-php/badge.svg?branch=master)](https://coveralls.io/github/pact-foundation/pact-php?branch=master)
![Compatibility Suite](https://github.com/pact-foundation/pact-php/actions/workflows/compatibility-suite.yml/badge.svg)
[![Packagist](https://img.shields.io/packagist/v/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)

[![Downloads](https://img.shields.io/packagist/dt/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)
[![Downloads This Month](https://img.shields.io/packagist/dm/pact-foundation/pact-php?style=flat-square)](https://packagist.org/packages/pact-foundation/pact-php)

#### Fast, easy and reliable testing for your APIs and microservices.

</span>

<br />
<p align="center">
  <a href="https://docs.pact.io"><img src="https://github.com/pact-foundation/pact-php/assets/3327643/d62310ef-4c9b-4bf1-ae84-a9ae41d88414" alt="Pact PHP Demo"/></a>
</p>
<br />

<table>
<tr>
<td>

**Pact** is the de-facto API contract testing tool. Replace expensive and brittle end-to-end integration tests with fast, reliable and easy to debug unit tests.

- ⚡ Lightning fast
- 🎈 Effortless full-stack integration testing - from the front-end to the back-end
- 🔌 Supports HTTP/REST and event-driven systems
- 🛠️ Configurable mock server
- 😌 Powerful matching rules prevents brittle tests
- 🤝 Integrates with Pact Broker / PactFlow for powerful CI/CD workflows
- 🔡 Supports 12+ languages

**Why use Pact?**

Contract testing with Pact lets you:

- ⚡ Test locally
- 🚀 Deploy faster
- ⬇️ Reduce the lead time for change
- 💰 Reduce the cost of API integration testing
- 💥 Prevent breaking changes
- 🔎 Understand your system usage
- 📃 Document your APIs for free
- 🗄 Remove the need for complex data fixtures
- 🤷‍♂️ Reduce the reliance on complex test environments

Watch our [series](https://www.youtube.com/playlist?list=PLwy9Bnco-IpfZ72VQ7hce8GicVZs7nm0i) on the problems with end-to-end integrated tests, and how contract testing can help.

</td>
</tr>
</table>

![----------](https://user-images.githubusercontent.com/53900/182992715-aa63e421-170b-41cf-8f95-82fe4b0846c2.png)

## Documentation

This readme offers a basic introduction to the library. The full documentation for Pact PHP and the rest of the framework is available at https://docs.pact.io/.

- [Installation](#installation)
- [Consumer Testing](/docs/consumer.md)
- [Provider Testing](/docs/provider.md)
- [Event Driven Systems](/docs/messages.md)
- [Examples](https://github.com/pact-foundation/pact-php/tree/master/examples/)
- [Stub Server](/docs/stub-server.md)
- [Framework Integrations](/docs/framework-integrations.md)
- [Troubleshooting](./docs/troubleshooting.md)

## Need Help

- [Join](http://slack.pact.io) our community [slack workspace](http://pact-foundation.slack.com/).
- Stack Overflow: https://stackoverflow.com/questions/tagged/pact
- Say 👋 on Twitter: [@pact_up]

## Installation

```shell
composer require pact-foundation/pact-php --dev

# 🚀 now write some tests!
```

Looking for the previous [stable 9.x.x release](https://github.com/pact-foundation/pact-php/tree/release/9.x)? 

### Requirements

PHP 8.1+ as of pact-php v10

### Do Not Track

In order to get better statistics as to who is using Pact, we have an anonymous tracking event that triggers when Pact installs for the first time. The only things we [track](https://docs.pact.io/metrics) are your type of OS, and the version information for the package being installed. No PII data is sent as part of this request. You can disable tracking by setting the environment variable `PACT_DO_NOT_TRACK=true`:

![----------](https://user-images.githubusercontent.com/53900/182992715-aa63e421-170b-41cf-8f95-82fe4b0846c2.png)

## Usage

### Writing a Consumer test

```php
namespace App\Tests;

use App\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class ConsumerServiceHelloTest extends TestCase
{
    public function testGetHelloString(): void
    {
        $matcher = new Matcher();

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
                'message' => $matcher->term('Hello, Bob', '(Hello, )[A-Za-z]+')
            ]);

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config = new MockServerConfig();
        $config
            ->setConsumer('jsonConsumer')
            ->setProvider('jsonProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('A get request to /hello/{name}')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes FFI calls to register the interaction and start the mock server.

        $service = new HttpClientService($config->getBaseUri()); // Pass in the URL to the Mock Server.
        $helloResult = $service->getHelloString('Bob'); // Make the real API request against the Mock Server.
        $verifyResult = $builder->verify(); // This will verify that the interactions took place.

        $this->assertTrue($verifyResult); // Make your assertions.
        $this->assertEquals('Hello, Bob', $helloResult);
    }
}
```

You can see (and run) the full version of this in `./examples/json`, as well as other examples in the parent folder.

To run the examples

1. Clone the repo `git@github.com:pact-foundation/pact-php.git`
2. Go to the repo `cd pact-php`
2. Install all dependencies `composer install`

Run a single example

`composer run-example:json`

Run all examples

`composer run-examples`

![----------](https://user-images.githubusercontent.com/53900/182992715-aa63e421-170b-41cf-8f95-82fe4b0846c2.png)

### Verifying a Provider

A provider test takes one or more pact files (contracts) as input, and Pact verifies that your provider adheres to the contract. In the simplest case, you can verify a provider as per below using a local pact file, although in practice you would usually use a Pact Broker to manage your contracts and CI/CD workflow.

```php
namespace App\Tests;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPactTest\Helper\PhpProcess;
use PHPUnit\Framework\TestCase;

class PactVerifyTest extends TestCase
{
    private PhpProcess $process;

    protected function setUp(): void
    {
        $this->process = new PhpProcess(__DIR__ . '/path/to/public/');
        $this->process->start();
    }

    protected function tearDown(): void
    {
        $this->process->stop();
    }

    /**
     * This test will run after the web server is started.
     */
    public function testPactVerifyConsumer()
    {
        $config = new VerifierConfig();
        $config->getProviderInfo()
            ->setName('jsonProvider') // Providers name to fetch.
            ->setHost('localhost')
            ->setPort($this->process->getPort());
        $config->getProviderState()
            ->setStateChangeUrl(new Uri(sprintf('http://localhost:%d/pact-change-state', $this->process->getPort())))
        ;
        if ($level = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($level);
        }

        $verifier = new Verifier($config);
        $verifier->addFile(__DIR__ . '/path/to/pacts/jsonConsumer-jsonProvider.json');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
```

It's best to run Pact verification tests as part of your unit testing suite, so you can readily access stubbing, IaC and other helpful tools.

![----------](https://user-images.githubusercontent.com/53900/182992715-aa63e421-170b-41cf-8f95-82fe4b0846c2.png)

## Compatibility

<details><summary>Versions</summary>

| Version | Status     | [Spec] Compatibility | PHP Compatibility | Install            |
| ------- | ---------- | -------------------- | ----------------- | ------------------ |
| 10.x    | Stable     | 1, 1.1, 2, 3, 4      | ^8.1              | See [installation] |
| 9.x     | Stable     | 1, 1.1, 2, 3\*       | ^8.0              | [9xx]              |
| 8.x     | Deprecated | 1, 1.1, 2, 3\*       | ^7.4\|^8.0        |                    |
| 7.x     | Deprecated | 1, 1.1, 2, 3\*       | ^7.3              |                    |
| 6.x     | Deprecated | 1, 1.1, 2, 3\*       | ^7.2              |                    |
| 5.x     | Deprecated | 1, 1.1, 2, 3\*       | ^7.1              |                    |
| 4.x     | Deprecated | 1, 1.1, 2            | ^7.1              |                    |
| 3.x     | Deprecated | 1, 1.1, 2            | ^7.0              |                    |
| 2.x     | Deprecated | 1, 1.1, 2            | >=7               |                    |
| 1.x     | Deprecated | 1, 1.1               | >=7               |                    |

_\*_ v3 support is limited to the subset of functionality required to enable language inter-operable [Message support].

</details>

<details><summary>Supported Platforms</summary>

| OS      | Architecture | Supported  | Pact-PHP Version |
| ------- | ------------ | ---------  | ---------------- |
| OSX     | x86_64       | ✅         | All              |
| Linux   | x86_64       | ✅         | All              |
| OSX     | arm64        | ✅         | 9.x +            |
| Linux   | arm64        | ✅         | 9.x +            |
| Windows | x86_64       | ✅         | All              |
| Windows | x86          | ✅         | 9.x -            |
| Alpine  | x86_64       | ✅         | All \*           |
| Alpine  | arm64        | ✅         | All \*           |

_\*_ For 9.x and below, supported with a workaround [Ruby Standalone with Alpine].

</details>

## Roadmap

The [roadmap](https://docs.pact.io/roadmap/) for Pact and Pact PHP is outlined on our main website.
## Contributing

See [CONTRIBUTING](CONTRIBUTING.md).

<a href="https://github.com/pact-foundation/pact-php/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=pact-foundation/pact-php" />
</a>
<br />

[spec]: https://github.com/pact-foundation/pact-specification
[9xx]: https://github.com/pact-foundation/pact-php/tree/release/9.x
[installation]: #installation
[message support]: https://github.com/pact-foundation/pact-specification/tree/version-3#introduces-messages-for-services-that-communicate-via-event-streams-and-message-queues
[Ruby Standalone with Alpine]: https://github.com/pact-foundation/pact-ruby-standalone/wiki/Using-the-pact-ruby-standalone-with-Alpine-Linux-Docker
