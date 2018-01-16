<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Matcher\LikeMatcher;
use PhpPact\Consumer\Service\MockServerHttpService;
use PhpPact\Consumer\Service\MockServerHttpServiceInterface;
use PhpPact\Core\Http\GuzzleClient;
use PhpPact\Core\Model\ConsumerRequest;
use PhpPact\Core\Model\ProviderResponse;
use PhpPact\Standalone\Installer\InstallManager;
use PHPUnit\Framework\TestCase;

class InteractionBuilderTest extends TestCase
{
    /** @var MockServerHttpServiceInterface */
    private $service;

    /** @var MockServer */
    private $mockServer;

    protected function setUp()
    {
        $config            = new MockServerEnvConfig();
        $installManager    = new InstallManager();
        $this->mockServer  = new MockServer($config, $installManager);
        $this->mockServer->start();
        $this->service = new MockServerHttpService(new GuzzleClient(), $config);
    }

    protected function tearDown()
    {
        $this->mockServer->stop();
    }

    public function testSimpleGet()
    {
        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('GET')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody([
                'message' => 'Hello, world!',
                'age'     => new LikeMatcher(73)
            ])
            ->addHeader('Content-Type', 'application/json');

        $builder = new InteractionBuilder(new MockServerEnvConfig());
        $result  = $builder
            ->given('A test request.')
            ->uponReceiving('A test response.')
            ->with($request)
            ->willRespondWith($response);

        $this->assertTrue($result);
    }

    public function testPostWithBody()
    {
        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('POST')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'someStuff'  => 'someOtherStuff',
                'someNumber' => 12,
                'anArray'    => [
                    12,
                    'words here',
                    493.5
                ]
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Hello, world!'
            ]);

        $builder = new InteractionBuilder(new MockServerEnvConfig());
        $result  = $builder
            ->given('A test request.')
            ->uponReceiving('A test response.')
            ->with($request)
            ->willRespondWith($response);

        $this->assertTrue($result);
    }
}
