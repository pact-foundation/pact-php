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
                'currentCity' => 'Austin',
            ]);

        $this->assertEquals('PUT', $model->getMethod());
        $this->assertEquals(['Content-Type' => ['application/json']], $model->getHeaders());
        $this->assertEquals('/somepath', $model->getPath());
        $this->assertEquals('{"currentCity":"Austin"}', $model->getBody());
    }
}
