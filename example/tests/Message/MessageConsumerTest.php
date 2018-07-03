<?php

namespace Consumer\Service;

use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class MessageConsumerTest extends TestCase
{

    /**
     * @throws \PhpPact\Standalone\Exception\MissingEnvVariableException
     */
    public function testMessage()
    {

        $builder    = new MessageBuilder();

        $content    = new \stdClass();
        $content->text = "Hello Mary!!";

        $metadata = ["queue"=>"wind cries"];

        $builder
            ->given('a hello message')
            ->expectsToReceive('an alligator named Mary exists')
            ->withMetadata($metadata)
            ->withContent($content);

        $output = $builder->reify();

        $this->assertTrue(false, "random assert");
    }
}
