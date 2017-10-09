<?php



use PHPUnit\Framework\TestCase;

final class ProviderTest extends TestCase
{
    public function testServerExists()
    {
        $http = new \Windwalker\Http\HttpClient();
        $uri = 'http://' . WEB_SERVER_HOST . ':' . WEB_SERVER_PORT . '/provider.php';

        $response = $http->get($uri);
        $status = $response->getStatusCode();

        $this->assertEquals(200, (int) $status, "Expect a 200 status code");
    }

    public function testPactProvider()
    {
        $uri = WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;

        $httpClient = new \Windwalker\Http\HttpClient();

        $pactVerifier = new \PhpPact\PactVerifier($uri);
        $hasException = false;
        try {
            $json = $this->getPactRoot() . DIRECTORY_SEPARATOR . 'mockapiconsumer-mockapiprovider.json';

            $pactVerifier->ProviderState("Test State")
                ->ServiceProvider("MockApiProvider", $httpClient)
                ->HonoursPactWith("MockApiConsumer")
                ->PactUri($json)
                ->Verify(null, "A GET request to get types");

            $pactVerifier->Verify(null, "A GET request to get variable types");

            $pactVerifier->Verify(null, "There is something to POST to");
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "Expect Pact to validate.");
    }

    public function testPactProviderStateSetupTearDown()
    {
        $uri = WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;

        $httpClient = new \Windwalker\Http\HttpClient();

        $pactVerifier = new \PhpPact\PactVerifier($uri);
        $hasException = false;

        $setUpFunction = function () {
            $fileName = "mock.json";
            $currentDir = dirname(__FILE__);
            $absolutePath = realpath($currentDir . '/../site/');
            $absolutePath .= '/' . $fileName;

            $type = new \stdClass();
            $type->id = 700;
            $type->name = "mock";
            $types = array( $type );
            $body = new \stdClass();

            $body->types = $types;

            $output = \json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($absolutePath, $output);
        };

        $tearDownFunction = function () {
            $fileName = "mock.json";
            $currentDir = dirname(__FILE__);
            $absolutePath = realpath($currentDir . '/../site/');
            $absolutePath .= '/' . $fileName;

            unlink($absolutePath);
        };

        try {
            $json = $this->getPactRoot() . DIRECTORY_SEPARATOR . 'mockapiconsumer-mockapiprovider.json';

            $pactVerifier->ProviderState("A GET request for a setup", $setUpFunction, $tearDownFunction)
                ->ServiceProvider("MockApiProvider", $httpClient)
                ->HonoursPactWith("MockApiConsumer")
                ->PactUri($json)
                ->Verify(); // note that this should test all as we can run setup and tear down
        } catch (\PhpPact\PactFailureException $e) {
            $hasException = true;
        }
        $this->assertFalse($hasException, "Expect Pact to validate.");
    }

    private function getPactRoot()
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'pact' . DIRECTORY_SEPARATOR;
        return realpath($dir);
    }
}
