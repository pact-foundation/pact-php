UPGRADE FROM 7.x to 8.0
=======================

 * Removed `InteractionBuilder::finalize`, use `InteractionBuilder::verify` instead
 * Removed `BuilderInterface::writePact`, use `BuilderInterface::verify` instead
 * Removed `PactConfigInterface`, use `MockServerConfigInterface` instead
 * [BC BREAK] Updated `MockServerConfigInterface`, removed methods related to http mock server
 * Removed environment variables:
    - PACT_LOG
    - PACT_MOCK_SERVER_HOST
    - PACT_MOCK_SERVER_PORT
    - PACT_CORS
    - PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT
    - PACT_MOCK_SERVER_HEALTH_CHECK_RETRY_SEC
    - PACT_BROKER_SSL_VERIFY
    - PACT_PROVIDER_NAME
 * [BC BREAK] Removed `MockServerConfig::getBaseUri()`, use `InteractionBuilder::getBaseUri()` instead
 * [BC BREAK] Need to call `MockServerConfigInterface::setProvider` in each test case
 * [BC BREAK] Added `InteractionBuilder::newInteraction`, required to be called before each interaction.
 * [BC BREAK] Removed `MockServer`, use `InteractionBuilder::createMockServer` instead
 * Allowed multiple interactions per mock server

   Example:
   ```php
   $builder = new InteractionBuilder($config);
   $builder
       ->newInteraction()
       ->given('a person exists')
       ->uponReceiving('a get request to /hello/{name}')
       ->with($request)
       ->willRespondWith($response);
   $builder
       ->newInteraction()
       ->given('a person exists')
       ->uponReceiving('a get request to /goodbye/{name}')
       ->with($request)
       ->willRespondWith($response);
   $builder->createMockServer();
   ```

 * Allowed multiple providers per consumer's test suite

   In a test case:
   ```php
   $config  = new MockServerEnvConfig();
   $config->setProvider('someProvider');
   $builder = new InteractionBuilder($config);
   ```

   In another test case:
   ```php
   $config  = new MockServerEnvConfig();
   $config->setProvider('otherProvider');
   $builder = new InteractionBuilder($config);
   ```

 * Removed `VerifierProcess`
 * Removed `MessageVerifier`, use `Verifier` instead
 * [BC BREAK] Move some methods from `VerifierConfigInterface` to:
    - UrlInterface
    - BrokerInterface
 * [BC BREAK] Updated `Verifier`

   Example:
   ```php
   $config = new VerifierConfig();
   $config
       ->setPort(8000)
       ->setProviderName('someProvider')
       ->setProviderVersion('1.0.0');

   $url = new Url();
   $url
       ->setUrl(new Uri('http://localhost'))
       ->setProviderName('someProvider')
       ->setUsername('user')
       ->setPassword('pass')
       ->setToken('token');

   $selectors = (new ConsumerVersionSelectors())
       ->addSelector('{"tag":"foo","latest":true}')
       ->addSelector('{"tag":"bar","latest":true}');

   $broker = new Broker();
   $broker
       ->setUrl(new Uri('http://localhost'))
       ->setProviderName('someProvider')
       ->setUsername('user')
       ->setPassword('pass')
       ->setToken('token')
       ->setConsumerVersionSelectors($selectors);

   $verifier = new Verifier();
   $verifier->newHandle($config);
   $verifier->addFile('C:\SomePath\consumer-provider.json');
   $verifier->addDirectory('C:\OtherPath');
   $verifier->addUrl($url);
   $verifier->addBroker($broker);

   $this->assertTrue($verifier->verify());
   ```

 * Removed `ProcessRunnerFactory`
 * Removed `PactMessage`
 * Removed `PactMessageConfig`, use `MockServerEnvConfig` or `MockServerConfig` instead
 * Removed `MockServerHttpService`
 * Removed `MockServerHttpServiceInterface`
 * Removed `HealthCheckFailedException`
 * Removed `BrokerHttpClient`
 * Removed `BrokerHttpClientInterface`
 * Added `MockServerNotStartedException`
 * Removed `ContractDownloader`
 * Removed `Model\Interaction`
 * Removed `Model\Message`
 * [BC BREAK] Updated `StubServerConfigInterface`, see [pact-stub-server](https://github.com/pact-foundation/pact-stub-server)
