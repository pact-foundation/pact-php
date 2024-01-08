<?php

namespace ProtobufSyncMessageConsumer;

use Grpc\BaseStub;
use Plugins\ShapeMessage;
use Plugins\AreaResponse;

class CalculatorClient extends BaseStub
{
    public function calculate(ShapeMessage $request, array $metadata = []): AreaResponse
    {
        [$response, $status] = $this->_simpleRequest(
            '/plugins.Calculator/calculate',
            $request,
            [AreaResponse::class, 'decode'],
            $metadata,
            []
        )->wait();

        return $response;
    }
}
