UPGRADE FROM 8.x to 9.0
=======================

* Environment Variables
  * These environment variables can be removed:
    * PACT_CORS
    * PACT_MOCK_SERVER_HEALTH_CHECK_TIMEOUT
    * PACT_MOCK_SERVER_HEALTH_CHECK_RETRY_SEC

* Verifier
  * Different pacts sources can be configured via `addXxx` methods

   Example Usage:
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

   $verifier = new Verifier($config);
   $verifier
      ->addFile('C:\SomePath\consumer-provider.json');
      ->addDirectory('C:\OtherPath');
      ->addUrl($url);
      ->addBroker($broker);

   $verifyResult = $verifier->verify();

   $this->assertTrue($verifyResult);
   ```

* Consumer
  * Pact file write mode has been changed from 'overwrite' to 'merge'. Make sure old pact files are removed before running tests.

   ```shell
   rm /path/to/pacts/*.json
   ```

  * Pact files now can ONLY be uploaded to Pact Broker by downloading and running Pact CLI manually.

   ```shell
   pact-broker publish /path/to/pacts/*.json --consumer-app-version 1.0.0 --branch main --broker-base-url https://test.pactflow.io --broker-token SomeToken
   ```
