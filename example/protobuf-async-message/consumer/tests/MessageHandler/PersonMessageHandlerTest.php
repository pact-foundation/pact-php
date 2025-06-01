<?php

namespace ProtobufAsyncMessageConsumer\Tests\MessageHandler;

use Library\Person;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Plugins\Protobuf\Factory\ProtobufMessageDriverFactory;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;
use ProtobufAsyncMessageConsumer\MessageHandler\PersonMessageHandler;
use ProtobufAsyncMessageConsumer\Service\SayHelloService;

class PersonMessageHandlerTest extends TestCase
{
    private SayHelloService $service;
    private string $given = 'Given';
    private string $surname = 'Surname';
    private Matcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Matcher(plugin: true);
        $service = $this->createMock(SayHelloService::class);
        $service
            ->expects($this->once())
            ->method('sayHello')
            ->with($this->given, $this->surname);
        $this->service = $service;
    }

    public function testInvoke(): void
    {
        $id = 'd1f077b5-0f91-40aa-b8f9-568b50ee4dd9';

        $config = (new PactMessageConfig())
            ->setConsumer('protobufAsyncMessageConsumer')
            ->setProvider('protobufAsyncMessageProvider')
            ->setPactSpecificationVersion('4.0.0')
            ->setPactDir(__DIR__ . '/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }

        $builder = new MessageBuilder($config, new ProtobufMessageDriverFactory());

        $builder
            ->given('A person with fixed id exists', ['id' => $id, 'reuse' => '0'])
            ->expectsToReceive('Person message sent')
            ->withContent(new Text(
                json_encode([
                    'pact:proto' => __DIR__ . '/../../../library/proto/say_hello.proto',
                    'pact:message-type' => 'Person',
                    'pact:content-type' => 'application/protobuf',
                    'id' => $this->matcher->regex($id, '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$'),
                    'name' => [
                        'given' => $this->matcher->like($this->given),
                        'surname' => $this->matcher->like($this->surname),
                    ],
                    'children' => [
                        'pact:match' => $this->matcher->matchAll(
                            [],
                            [
                                $this->matcher->eachKey(
                                    [],
                                    [$this->matcher->string()]
                                ),
                                $this->matcher->eachValue(
                                    [],
                                    [
                                        $this->matcher->number(23),
                                    ],
                                ),
                                $this->matcher->atLeast(1),
                                $this->matcher->atMost(3),
                                // These matchers are allowed, but are not recommend
                                // $this->matcher->atLeastLike(123, 2),
                                // $this->matcher->atMostLike(234, 3),
                            ],
                        ),
                        'Zane' => 12,
                    ],
                    // We can use constrainedArrayLike, atLeastLike or atMostLike to match a list of values,
                    // no need to write a lot of verbose code like matchAll
                    'hobbies' => $this->matcher->constrainedArrayLike('painting', 2, 5),
                ]),
                'application/protobuf'
            ));

        $builder->setCallback(function (string $pactJson): void {
            $message = \json_decode($pactJson);
            $person = new Person();
            $decoded = base64_decode($message->contents->content);
            $person->mergeFromString($decoded);
            $handler = new PersonMessageHandler($this->service);
            $handler($person);
        });

        $this->assertTrue($builder->verify());
    }
}
