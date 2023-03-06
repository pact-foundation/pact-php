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

* Stub Server
  * Endpoint now can be set by:
  ```php
  $service = new StubServerHttpService(new GuzzleClient(), $this->config);
  $service->getJson($endpoint);
  ```
