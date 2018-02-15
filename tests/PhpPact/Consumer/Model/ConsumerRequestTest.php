<?php

namespace PhpPact\Consumer\Model;

use PHPUnit\Framework\TestCase;

class ConsumerRequestTest extends TestCase
{
    public function testSerializing()
    {
        $model = new ConsumerRequest();
        $model
            ->setMethod('PUT')
            ->setPath('/somepath')
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'currentCity'    => 'Austin'
            ]);

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        $this->assertEquals('PUT', $data['method']);
        $this->assertEquals('application/json', $data['headers']['Content-Type']);
        $this->assertEquals('/somepath', $data['path']);
        $this->assertEquals('Austin', $data['body']['currentCity']);
    }
}
