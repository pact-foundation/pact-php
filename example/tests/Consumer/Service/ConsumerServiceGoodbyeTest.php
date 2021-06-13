<?php

namespace Consumer\Service;

use PhpPact\Consumer\Exception\MockServerNotStartedException;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\Installer\Exception\FileDownloadFailureException;
use PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class ConsumerServiceGoodbyeTest extends TestCase
{
    /**
     * @throws NoDownloaderFoundException
     * @throws FileDownloadFailureException
     * @throws MockServerNotStartedException
     */
    public function testGetGoodbyeString()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/goodbye/Bob')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Goodbye, Bob'
            ]);

        $config      = new MockServerEnvConfig();
        $config->setProvider('someProvider');
        $builder     = new InteractionBuilder($config);
        $builder
            ->newInteraction()
            ->given('Get Goodbye')
            ->uponReceiving('A get request to /goodbye/{name}')
            ->with($request)
            ->willRespondWith($response)
            ->createMockServer();

        $service = new HttpClientService($builder->getBaseUri());
        $result  = $service->getGoodbyeString('Bob');

        $this->assertTrue($builder->verify());
        $this->assertEquals('Goodbye, Bob', $result);
    }
}
