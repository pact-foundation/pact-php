# Usage for the optional `pact-stub-service`

If you would like to test with fixtures, you can use the `pact-stub-service` like this:

```php
$files    = [__DIR__ . '/someconsumer-someprovider.json'];
$port     = 7201;
$endpoint = 'test';

$config = (new StubServerConfig())
            ->setFiles($files)
            ->setPort($port);

$stubServer = new StubServer($config);
$stubServer->start();

$client = new \GuzzleHttp\Client();

$response = $client->get($this->config->getBaseUri() . '/' . $endpoint);

echo $response->getBody(); // output: {"results":[{"name":"Games"}]}
```
