<?php

namespace PhpPact\Consumer\Model;

use PhpPact\Consumer\Matcher\Matcher;
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

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        $this->assertEquals('PUT', $data['method']);
        $this->assertEquals('application/json', $data['headers']['Content-Type']);
        $this->assertEquals('/somepath', $data['path']);
        $this->assertEquals('Austin', $data['body']['currentCity']);
    }

    public function testSerializingWhenPathUsingMatcher()
    {
        $matcher = new Matcher();
        $pathVariable = '474d610b-c6e3-45bd-9f70-529e7ad21df0';
        $model = new ConsumerRequest();
        $model
            ->setMethod('PATCH')
            ->setPath($matcher->regex("/somepath/$pathVariable/status", '\/somepath\/[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}\/status'))
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'status' => 'finished',
            ]);

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        $this->assertEquals('PATCH', $data['method']);
        $this->assertEquals('application/json', $data['headers']['Content-Type']);
        $this->assertIsArray($data['path']);
        $this->assertArrayHasKey('data', $data['path']);
        $this->assertArrayHasKey('json_class', $data['path']);
        $this->assertEquals('finished', $data['body']['status']);
    }
}
