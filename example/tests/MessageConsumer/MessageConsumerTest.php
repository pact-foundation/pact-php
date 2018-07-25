<?php

namespace MessageConsumer;

require_once __DIR__ . '/../../src/MessageConsumer/MessageConsumer.php';

use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Http\GuzzleClient;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageConsumerTest
 */
class MessageConsumerTest extends TestCase
{
    private static $config;

    public static function setUpBeforeClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUpBeforeClass();

        self::$config = (new PactMessageConfig())
                        ->setConsumer('test_consumer')
                        ->setProvider('test_provider')
                        ->setPactDir(__DIR__ . '/../../output/');
    }

    public static function tearDownAfterClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::tearDownAfterClass();

        // build out brokerHttpService as your example
        /*
        $brokerHttpService = new BrokerHttpClient(new GuzzleClient(), new Uri($pactBrokerUri));
        $brokerHttpService->publishJson($json, $consumerVersion);
        $brokerHttpService->tag($this->mockServerConfig->getConsumer(), $consumerVersion, $tag);
        */
    }

    /**
     * @throws \Exception
     */
    public function testProcess()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new \stdClass();
        $contents->test = 'Hello Mary!!';

        $metadata = ['queue'=>'wind cries', 'routing_key'=>'wind cries'];

        $builder
            ->given('a hello message')
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new MessageConsumer();
        $callback        = [$consumerMessage, 'Process'];
        $builder->setCallback($callback);

        $hasException = false;

        try {
            $builder->verify();
        } catch (\Exception $e) {
            $hasException = true;
        }

        $this->assertFalse($hasException, 'Expects verification to pass without exceptions being thrown');
    }

    /**
     * @throws \Exception
     */
    public function testProcessMessage2()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new \stdClass();
        $contents->song = 'And the wind whispers Mary';

        $metadata = ['queue'=>'And the clowns have all gone to bed', 'routing_key'=>'And the clowns have all gone to bed'];

        $builder
            ->given('You can hear happiness staggering on down the street')
            ->expectsToReceive('footprints dressed in red')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new MessageConsumer();
        $callback        = [$consumerMessage, 'ProcessAnotherMessageType'];
        $builder->setCallback($callback);

        $hasException = false;

        try {
            $builder->verify();
        } catch (\Exception $e) {
            $hasException = true;
        }

        $this->assertFalse($hasException, 'Expects verification to pass without exceptions being thrown');
    }
}
