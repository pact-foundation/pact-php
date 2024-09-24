<?php

namespace GraphqlConsumer\Tests\Service;

use PhpPact\Consumer\Matcher\Matcher;
use GraphqlConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public function testQuery()
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('POST')
            ->setPath('/api')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'query' => <<<GRAPHQL
                query(\$message: String!) {
                    echo(message: \$message)
                }
                GRAPHQL,
                'variables' => [
                    'message' => $matcher->string('Hello World'),
                ],
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'data' => [
                    'echo' => $matcher->string('Greetings Universe'),
                ],
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('graphqlConsumer')
            ->setProvider('graphqlProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('User exist')
            ->uponReceiving('A query request to /api')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $result = $service->query();
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertJson($result);
        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                'echo' => 'Greetings Universe',
            ],
        ]), $result);
    }

    public function testMutation()
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('POST')
            ->setPath('/api')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'query' => <<<GRAPHQL
                mutation(\$x: Int!, \$y: Int!) {
                    sum(
                        x: \$x,
                        y: \$y
                    )
                }
                GRAPHQL,
                'variables' => [
                    'x' => $matcher->integerV3(2),
                    'y' => $matcher->integerV3(2),
                ],
            ]);

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'data' => [
                    'sum' => $matcher->integerV3(4),
                ],
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('graphqlConsumer')
            ->setProvider('graphqlProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('A mutation request to /api')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $result = $service->mutation();
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertJson($result);
        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                'sum' => 4,
            ],
        ]), $result);
    }
}
