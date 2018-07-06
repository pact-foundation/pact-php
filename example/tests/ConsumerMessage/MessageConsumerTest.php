<?php

namespace Consumer\Service;

require_once __DIR__ . '/../../src/ConsumerMessage/ConsumerMessage.php';

use ConsumerMessage\ConsumerMessage;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PHPUnit\Framework\TestCase;

class MessageConsumerTest extends TestCase
{
    /**
     * @throws \PhpPact\Standalone\Exception\MissingEnvVariableException
     */
    public function testMessage()
    {
        $config = new PactMessageConfig();
        $config->setConsumer('test_consumer');
        $config->setProvider('test_provider');
        $config->setPactDir('D:\\Temp\\');

        $builder    = new MessageBuilder($config);

        $content       = new \stdClass();
        $content->text = 'Hello Mary!!';

        $metadata = ['queue'=>'wind cries', 'routing_key'=>'wind cries'];

        $builder
            ->given('a hello message')
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($content);

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
