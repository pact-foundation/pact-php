# UPGRADE FROM 9.x to 10.0

We have migrated from the pact-ruby core, to the pact-reference(rust) core.

This migrates from a CLI driven process for the Pact Framework, to an FFI process based framework.

- Pre-requisites

  - PHP 8.x +
  - PHP FFI Extension installed

- Environment Variables

  - These environment variables are no longer required can be removed:
    - PACT_CORS
    - PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT
    - PACT_MOCK_SERVER_HEALTH_CHECK_RETRY_SEC

- Consumer

  - The `PhpPact\Consumer\Listener\PactTestListener` listener should be removed from your phpunit config
  - Default Pact file write mode has been changed from 'overwrite' to 'merge'. Make sure old pact files are removed before running tests.

  ```shell
  rm /path/to/pacts/*.json
  ```

  - Pact files now can ONLY be uploaded to Pact Broker by downloading and running Pact CLI manually.

  ```shell
  pact-broker publish /path/to/pacts/*.json --consumer-app-version 1.0.0 --branch main --broker-base-url https://test.pactflow.io --broker-token SomeToken
  ```

- Verifier

  - `$config->setProviderName("providerName")` is now available via `$config->getProviderInfo()->setName("backend")`
    - This is further chainable with the following options:-
      - `->setHost('localhost')`
      - `->setPort('8080')`
      - `->setScheme('http')`
      - `->setPath('/')`
  - Different pacts sources can be configured via `addXxx` methods
    - NB:- You must add at least one source, otherwise the verifier will pass, but not verify any Pact files.
    - Types:-
      - `addUrl` - Verify Provider by Pact Url retrieved by Broker (Webhooks)
      - `addBroker` Verify Provider by dynamically fetched Pacts (Provider change)
      - `addFile` / `addDir` - Verify Provider by local file or directory
  - `$config->addCustomProviderHeader("headerName", "headerValue")` is now available via `$config->getCustomHeaders()->addHeader("headerName", "headerValue")`

  Example Usage:

  ```php
       $config = new VerifierConfig();
       $config
           ->setLogLevel('DEBUG');
       $config
           ->getProviderInfo()
           ->setName("personProvider")
           ->setHost('localhost')
           ->setPort('8080')
           ->setScheme('http')
           ->setPath('/');

       if ($isCi = getenv('CI')) {
           $publishOptions = new PublishOptions();
           $publishOptions
               ->setProviderVersion(exec('git rev-parse --short HEAD'))
               ->setProviderBranch(exec('git rev-parse --abbrev-ref HEAD'));
           $config->setPublishOptions($publishOptions);
       }

       $broker = new Broker();
       $broker->setUsername(getenv('PACT_BROKER_USERNAME'));
       $broker->setPassword(getenv('PACT_BROKER_PASSWORD'));
       $broker->setUsername(getenv('PACT_BROKER_TOKEN'));
       $verifier = new Verifier($config);

       // 1. verify with a broker, but using a pact url to verify a specific pact
       // PACT_URL=http://localhost:9292/pacts/provider/personProvider/consumer/personConsumer/latest
       if ($pact_url = getenv('PACT_URL')) {
           $url = new Url();
           $url->setUrl(new Uri($pact_url));
           $verifier->addUrl($url);
       }
       // 2. verify files from local directory or file
       //    results will not be published
       else if ($pactDir = getenv('PACT_DIR')) {
           $verifier->addDirectory($pactDir);
       } else if ($pactFile = getenv('PACT_FILE')) {
           $verifier->addFile($pactFile);
       } else {
           // 2. verify with broker by fetching dynamic pacts (with consumer version selectors)
           // if you don't setConsumerVersionSelectors then it will fetch the latest pact for the named provider
           if ($pactBrokerBaseUrl = getenv('PACT_BROKER_BASE_URL')) {
               $broker->setUrl(new Uri($pactBrokerBaseUrl));
           } else {
               $broker->setUrl(new Uri('http://localhost:9292'));
           }
           // we need to set the provider branch here for PactBrokerWithDynamicConfiguration
           // as $publishOptions->setProviderBranch value set above isn't used.
           $broker->setProviderBranch(exec('git rev-parse --abbrev-ref HEAD'));
           $selectors = (new ConsumerVersionSelectors())
               ->addSelector(new Selector(mainBranch: true))
               ->addSelector(new Selector(deployedOrReleased: true));
           $broker->setConsumerVersionSelectors($selectors);
           $broker->setEnablePending(true);
           $broker->setIncludeWipPactSince('2020-01-30');
           $verifier->addBroker($broker);
       }


       $verifyResult = $verifier->verify();

       $this->assertTrue($verifyResult);
  ```

- Stub Server

  - No longer defaults to port 7201, picks free port at random.
  - `PhpPact\Standalone\StubService\Service\StubServerHttpService` is no longer available. Guzzle can be used to request to stub server directly:

  ```php
  $client = new \GuzzleHttp\Client();
  $response = $client->get($this->config->getBaseUri() . '/' . $endpoint);
  echo $response->getBody();
  ```

- Example Migrations to 10.x (Pull Request Diffs)
  - PHP Verifier https://github.com/acmachado14/simple-pact/compare/main...YOU54F:simple-pact:ffi-next
  - PHP Consumer https://github.com/YOU54F/pact-testing/compare/main...YOU54F:pact-testing:ffi-next
  - PHP Consumer & Verifier
    - Consumer https://github.com/YOU54F/014-pact-http-consumer-php/compare/main...YOU54F:014-pact-http-consumer-php:ffi-next
    - Verifier https://github.com/YOU54F/015-pact-http-producer-php/compare/main...YOU54F:015-pact-http-producer-php:ffi-next


Examples of Additional Features now possible

- [CSV](./example/csv/)
- [Protobuf Async Message](./example/protobuf-async-message/)
- [Protobuf Sync Message/gRPC](./example/protobuf-sync-message/)
- [XML](./example/xml/)
- [Binary](./example/binary/)
- [Multipart](./example/multipart/)
