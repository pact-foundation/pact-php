<?php

namespace ProtobufSyncMessageConsumer;

use Plugins\AreaResponse;
use Plugins\ShapeMessage;
use Grpc\ChannelCredentials;

class ProtobufClient
{
    public function __construct(private string $baseUrl)
    {
    }

    public function calculate(ShapeMessage $shapeMessage): AreaResponse
    {
        $client = new CalculatorClient($this->baseUrl, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        return $client->calculate($shapeMessage);
    }
}
