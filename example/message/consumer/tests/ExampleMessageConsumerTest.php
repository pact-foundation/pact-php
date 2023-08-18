<?php

namespace MessageConsumer\Tests;

use Exception;
use MessageConsumer\ExampleMessageConsumer;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExampleMessageConsumerTest extends TestCase
{
    private static PactConfigInterface $config;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$config = (new PactMessageConfig())
                        ->setConsumer('messageConsumer')
                        ->setProvider('messageProvider')
                        ->setPactDir(__DIR__.'/../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            self::$config->setLogLevel($logLevel);
        }
    }

    /**
     * @throws Exception
     */
    public function testProcessText()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new stdClass();
        $contents->text = 'Hello Mary';

        $metadata = ['queue' => 'wind cries', 'routing_key' => 'wind cries'];

        $builder
            ->given('a message', ['foo' => 'bar'])
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'ProcessText'];
        $builder->setCallback($callback);

        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
    }

    /**
     * @throws Exception
     */
    public function testProcessSong()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new stdClass();
        $contents->song = 'And the wind whispers Mary';

        $metadata = ['queue' => 'And the clowns have all gone to bed', 'routing_key' => 'And the clowns have all gone to bed'];

        $builder
            ->given('You can hear happiness staggering on down the street')
            ->expectsToReceive('footprints dressed in red')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'ProcessSong'];
        $builder->setCallback($callback);

        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
    }
}
