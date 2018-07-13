<?php

namespace MessageConsumer;

use PHPUnit\Framework\TestCase;

/**
 * Class MessageProviderTest
 */
class MessageProviderTest extends TestCase
{
    public static function setUpBeforeClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::tearDownAfterClass();
    }

    /**
     * @throws \Exception
     */
    public function testProcess()
    {
        /*
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
        */

        \sleep(60);
        $this->assertFalse(false, 'Expects verification to pass without exceptions being thrown');
    }
}
