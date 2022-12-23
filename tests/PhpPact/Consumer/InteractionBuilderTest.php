<?php

namespace PhpPact\Consumer;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class InteractionBuilderTest extends TestCase
{
    /**
     * @throws MissingEnvVariableException
     * @throws \Exception
     */
    public function testSimpleGet()
    {
        $matcher = new Matcher();

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
                'age'     => $matcher->like(73),
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

    /**
     * @throws MissingEnvVariableException
     */
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
                    493.5,
                ],
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Hello, world!',
            ]);

        $builder = new InteractionBuilder(new MockServerEnvConfig());
        $result  = $builder
            ->given('A test request.')
            ->uponReceiving('A test response.')
            ->with($request)
            ->willRespondWith($response);

        $this->assertTrue($result);
    }

    /**
     * @throws MissingEnvVariableException
     */
    public function testBuildWithEachLikeMatcher()
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setPath('/something')
            ->setMethod('GET')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'list' => $matcher->eachLike([
                    'test'    => 1,
                    'another' => 2,
                ]),
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
