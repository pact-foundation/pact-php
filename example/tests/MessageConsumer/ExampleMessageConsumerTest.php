<?php

namespace MessageConsumer;

require_once __DIR__ . '/../../src/MessageConsumer/ExampleMessageConsumer.php';

use Exception;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\PactConfigInterface;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class ExampleMessageConsumerTest
 */
class ExampleMessageConsumerTest extends TestCase
{
    private static PactConfigInterface $config;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$config = (new PactMessageConfig())
                        ->setConsumer('test_consumer')
                        ->setProvider('test_provider')
                        ->setPactDir(__DIR__ . '/../../output/');
    }

    public static function tearDownAfterClass(): void
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
     * @throws Exception
     */
    public function testProcessText()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new stdClass();
        $contents->text = 'Hello Mary';

        $metadata = ['queue'=>'wind cries', 'routing_key'=>'wind cries'];

        $builder
            ->given('a message', ['foo'])
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'ProcessText'];
        $builder->setCallback($callback);

        $hasException = false;

        $builder->verify();

        $this->assertTrue(true, 'Expects to reach this true statement by running verify()');
    }

    /**
     * @throws Exception
     */
    public function testProcessSong()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new stdClass();
        $contents->song = 'And the wind whispers Mary';

        $metadata = ['queue'=>'And the clowns have all gone to bed', 'routing_key'=>'And the clowns have all gone to bed'];

        $builder
            ->given('You can hear happiness staggering on down the street')
            ->expectsToReceive('footprints dressed in red')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'ProcessSong'];
        $builder->setCallback($callback);

        $hasException = false;

        try {
            $builder->verify();
        } catch (Exception $e) {
            $hasException = true;
        }

        $this->assertFalse($hasException, 'Expects verification to pass without exceptions being thrown');
    }
}
