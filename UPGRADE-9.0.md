UPGRADE FROM 8.x to 9.0
=======================

* Interaction Builder
  * It's now required to call `PhpPact\Consumer\InteractionBuilder::createMockServer` manually

   Example Usage:
   ```php
   $builder = new InteractionBuilder($config);
   $builder
       ->given('a person exists')
       ->uponReceiving('a get request to /hello/{name}')
       ->with($request)
       ->willRespondWith($response);
   $builder->createMockServer();

   $apiClient->sendRequest();

   $this->assertTrue($builder->verify());
   ```
