<?php

namespace BinaryConsumer\Tests\Service;

use BinaryConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public function testGetImageContent()
    {
        $path = __DIR__ . '/../_resource/image.jpg';

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/image.jpg')
            ->addHeader('Accept', 'image/jpeg');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'image/jpeg')
            ->setBody(new Binary($path, 'image/jpeg'));

        $config = new MockServerConfig();
        $config
            ->setConsumer('binaryConsumer')
            ->setProvider('binaryProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Image file image.jpg exists')
            ->uponReceiving('A get request to /image.jpg')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $imageContentResult = $service->getImageContent();
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertEquals(file_get_contents($path), $imageContentResult);
    }
}
