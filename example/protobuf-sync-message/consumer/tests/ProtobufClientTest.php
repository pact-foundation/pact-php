<?php

namespace ProtobufSyncMessageConsumer\Tests;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Plugins\Protobuf\Factory\ProtobufSyncMessageDriverFactory;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\SyncMessage\SyncMessageBuilder;
use PHPUnit\Framework\TestCase;
use Plugins\Rectangle;
use Plugins\ShapeMessage;
use ProtobufSyncMessageConsumer\ProtobufClient;

class ProtobufClientTest extends TestCase
{
    public function testCalculateArea(): void
    {
        $matcher = new Matcher(plugin: true);
        $protoPath = __DIR__ . '/../../library/proto/area_calculator.proto';

        $config = new MockServerConfig();
        $config->setConsumer('protobufSyncMessageConsumer');
        $config->setProvider('protobufSyncMessageProvider');
        $config->setPactSpecificationVersion('4.0.0');
        $config->setPactDir(__DIR__.'/../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $config->setHost('127.0.0.1');
        $builder = new SyncMessageBuilder($config, new ProtobufSyncMessageDriverFactory());
        $builder
            ->expectsToReceive('request for calculate shape area')
            ->withMetadata([])
            ->withContent(new Text(
                json_encode([
                    'pact:proto' => $protoPath,
                    'pact:content-type' => 'application/grpc',
                    'pact:proto-service' => 'Calculator/calculate',

                    'request' => [
                        'rectangle' => [
                            'length' => $matcher->number(3),
                            'width' => $matcher->number(4),
                        ],
                    ],
                    'response' => [
                        'value' => $matcher->number(12),
                    ]
                ]),
                'application/grpc'
            ));
        $builder->registerMessage();

        $service = new ProtobufClient("{$config->getHost()}:{$config->getPort()}");
        $rectangle = (new Rectangle())->setLength(3)->setWidth(4);
        $message = (new ShapeMessage())->setRectangle($rectangle);
        $response = $service->calculate($message);

        $this->assertTrue($builder->verify());
        $this->assertEquals(3 * 4, $response->getValue());
    }
}
