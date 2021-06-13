<?php

namespace MessageConsumer;

use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\MockService\MockServerConfigInterface;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class ExampleMessageConsumerTest
 */
class ExampleMessageConsumerTest extends TestCase
{
    private static MockServerConfigInterface $config;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$config = (new MockServerEnvConfig())
                        ->setConsumer('test_consumer')
                        ->setProvider('test_provider')
                        ->setPactDir(__DIR__ . '/../../output/');
    }

    /**
     * @throws \Exception
     */
    public function testProcessText()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new \stdClass();
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

        $this->assertTrue($builder->verify());
    }

    /**
     * @throws \Exception
     */
    public function testProcessSong()
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
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'ProcessSong'];
        $builder->setCallback($callback);

        $this->assertTrue($builder->verify());
    }
}
