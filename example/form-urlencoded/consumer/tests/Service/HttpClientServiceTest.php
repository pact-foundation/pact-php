<?php

namespace FormUrlEncodedConsumer\Tests\Service;

use PhpPact\Consumer\Matcher\Generators\RandomInt;
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
            ->addHeader('Accept', 'application/x-www-form-urlencoded')
            ->setBody(
                new Text(
                    json_encode([
                        'empty' => $matcher->equal(''),
                        'agree' => $matcher->regex('false', 'true|false'),
                        'fullname' => $matcher->string('User name'),
                        'email' => $matcher->email('user@email.test'),
                        'password' => $matcher->regex('user@password111', '^[\w\d@$!%*#?&^_-]{8,}$'),
                        'age' => $matcher->number(27),
                        'roles[]' => $matcher->eachValue(['User'], [$matcher->regex('User', 'Admin|User|Manager')]),
                        'orders[]' => $matcher->arrayContaining([
                            $matcher->equal('DESC'),
                            $matcher->equal('ASC'),
                            $matcher->equal(''),
                        ]),
                        // Empty string keys are supported
                        '' => ['first', 'second', 'third'],
                        // Null, boolean and object values are not supported, so the values and matchers will be ignored
                        'null' => $matcher->nullValue(),
                        'boolean' => $matcher->booleanV3(true),
                        'object' => $matcher->like([
                            'key' => $matcher->string('value')
                        ]),
                        // special characters are encoded
                        'ampersand' => $matcher->equal('&'),
                        'slash' => '/',
                        'question-mark' => '?',
                        'equals-sign' => '=',
                        '&' => 'ampersand',
                        '/' => 'slash',
                        '?' => 'question-mark',
                        '=' => 'equals-sign',
                    ]),
                    'application/x-www-form-urlencoded'
                )
            )
        ;

        $response = new ProviderResponse();
        $response
            ->setStatus(201)
            ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->setBody(
                new Text(
                    json_encode([
                        'id' => $matcher->uuid(),
                        'age' => $matcher->integerV3()->withGenerator(new RandomInt(0, 130)),
                        'name[]' => [
                            $matcher->regex(null, $gender = 'Mr\.|Mrs\.|Miss|Ms\.'),
                            $matcher->string(),
                            $matcher->string(),
                            $matcher->string(),
                        ],
                    ]),
                    'application/x-www-form-urlencoded'
                )
            );

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
        parse_str($service->createUser(), $params);
        $verifyResult = $builder->verify();

        $this->assertTrue(condition: $verifyResult);
        $this->assertArrayHasKey('id', $params);
        $pattern = Matcher::UUID_V4_FORMAT;
        $this->assertMatchesRegularExpression("/{$pattern}/", $params['id']);
        $this->assertArrayHasKey('age', $params);
        $this->assertLessThanOrEqual(130, $params['age']);
        $this->assertGreaterThanOrEqual(0, $params['age']);
        $this->assertArrayHasKey('name', $params);
        $this->assertIsArray($params['name']);
        $this->assertCount(4, $params['name']);
        $this->assertMatchesRegularExpression("/{$gender}/", $params['name'][0]);
    }
}
