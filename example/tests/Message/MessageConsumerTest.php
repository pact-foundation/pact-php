<?php

namespace Consumer\Service;

use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class MessageConsumerTest extends TestCase
{

    public function testMessage()
    {

        $config     = new MockServerEnvConfig();
        $builder    = new MessageBuilder($config);

        $content    = new \stdClass();
        $content->text = "Hello Mary!!";

        $builder
            ->given('a hello message')
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($content)
            ->withContent($content);


        $this->assertTrue(false, "random assert");
    }
}
