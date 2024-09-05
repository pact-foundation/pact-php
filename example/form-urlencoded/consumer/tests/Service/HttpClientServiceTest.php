<?php

namespace FormUrlEncodedConsumer\Tests\Service;

use PhpPact\Consumer\Matcher\Matcher;
use FormUrlEncodedConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public function testGetMovies()
    {
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('POST')
            ->setPath('/users')
            ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->addHeader('Accept', 'application/json')
            ->setBody(
                new Text(
                    json_encode([
                        'null' => $matcher->nullValue(),
                        'empty' => $matcher->equal(''),
                        'agree' => $matcher->regex('false', 'true|false'),
                        'fullname' => $matcher->string('User name'),
                        'email' => $matcher->email('user@email.test'),
                        'password' => $matcher->regex('user@password111', '^[\w\d@$!%*#?&^_-]{8,}$'),
                        'age' => $matcher->number(27),
                        'roles[]' => $matcher->eachValue(['User'], [$matcher->regex('User', 'Admin|User|Manager')]),
                        // Boolean value is not supported, and will panic
                        // 'boolean' => $matcher->booleanV3(true),
                        // Object value is not supported, and will panic
                        // 'object' => $matcher->like([
                        //     'key' => $matcher->string('value',)
                        // ]),
                    ]),
                    'application/x-www-form-urlencoded'
                )
            )
        ;

        $response = new ProviderResponse();
        $response
            ->setStatus(201)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'id' => $matcher->uuid('6e58b1df-ff80-4031-b7b9-5191e4c74ee8'),
            ]);

        $config = new MockServerConfig();
        $config
            ->setConsumer('formUrlEncodedConsumer')
            ->setProvider('formUrlEncodedProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Endpoint is protected')
            ->uponReceiving('A post request to /users')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $body = json_decode($service->createUser(), true);
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertArrayHasKey('id', $body);
        $pattern = Matcher::UUID_V4_FORMAT;
        $this->assertEquals(1, preg_match("/{$pattern}/", $body['id']));
    }
}
