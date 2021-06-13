<?php

namespace PhpPact\Consumer;

use Exception;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class MessageBuilderTest extends TestCase
{
    private MessageBuilder $builder;
    private string $consumer = 'test-message-consumer';
    private string $provider = 'test-message-provider';
    private string $dir      = __DIR__ . '/../../_output';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $config = new MockServerConfig();
        $config->setProvider($this->provider);
        $config->setConsumer($this->consumer);
        $config->setPactDir($this->dir);
        $config->setPactSpecificationVersion('3.0.0');
        $this->builder = new MessageBuilder($config);
    }

    /**
     * @throws Exception
     */
    public function testMessage()
    {
        $contents       = new \stdClass();
        $contents->song = 'And the wind whispers Mary';

        $metadata = ['queue' => 'And the clowns have all gone to bed', 'routing_key' => 'And the clowns have all gone to bed'];

        $this->builder
            ->given('You can hear happiness staggering on down the street')
            ->expectsToReceive('footprints dressed in red')
            ->withMetadata($metadata)
            ->withContent($contents);

        $this->builder->setCallback(function (string $message) use ($contents) {
            $obj = \json_decode($message);
            $this->assertEquals($contents, $obj->contents);
        });

        $this->assertTrue($this->builder->verify());
    }
}
