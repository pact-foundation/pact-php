<?php

namespace Consumer\Service;

require_once __DIR__ . '/../../src/ConsumerMessage/ConsumerMessage.php';

use ConsumerMessage\ConsumerMessage;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageConsumerTest
 */
class MessageConsumerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testMessage()
    {
        $config = new PactMessageConfig();
        $config->setConsumer('test_consumer');
        $config->setProvider('test_provider');
        $config->setPactDir('D:\\Temp\\');

        $builder    = new MessageBuilder($config);

        $contents       = new \stdClass();
        $contents->test = 'Hello Mary!!';

        $metadata = ['queue'=>'wind cries', 'routing_key'=>'wind cries'];

        $builder
            ->given('a hello message')
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($contents);

        // established mechanism to this via callbacks
        $consumerMessage = new ConsumerMessage();
        $callback        = [$consumerMessage, 'Process'];
        $builder->setCallback($callback);
        $builder->verify();

        // update message interaction
        //$builder->reify();
        //$builder->finalize();

        $this->assertTrue(true, 'random assert');
    }
}
