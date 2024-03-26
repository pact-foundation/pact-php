<?php

namespace MessageConsumer\Tests;

use Exception;
use MessageConsumer\ExampleMessageConsumer;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExampleMessageConsumerTest extends TestCase
{
    private static PactConfigInterface $config;
    private Matcher $matcher;

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

    public function setUp(): void
    {
        $this->matcher = new Matcher();
    }

    /**
     * @throws Exception
     */
    public function testProcessText()
    {
        $builder    = new MessageBuilder(self::$config);

        $contents       = new stdClass();
        $contents->text = 'Hello Mary';
        $contents->number = $this->matcher->integerV3();

        $metadata = ['queue' => 'wind cries', 'routing_key' => $this->matcher->string()];

        $builder
            ->given('a message', ['foo' => 'bar'])
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ExampleMessageConsumer();
        $callback        = [$consumerMessage, 'processMessage'];
        $builder->setCallback($callback);

        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
    }
}
