<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
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
        $this->assertEquals(['Content-Type' => 'application/json'], $model->getHeaders());
        $this->assertEquals('/somepath', $model->getPath());
        $this->assertEquals('{"currentCity":"Austin"}', $model->getBody());
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

        $this->assertEquals('PATCH', $model->getMethod());
        $this->assertEquals(['Content-Type' => 'application/json'], $model->getHeaders());
        $this->assertEquals('{"value":"\/somepath\/474d610b-c6e3-45bd-9f70-529e7ad21df0\/status","regex":"\\\\\\/somepath\\\\\\/[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}\\\\\\/status","pact:matcher:type":"regex"}', $model->getPath());
        $this->assertEquals('{"status":"finished"}', $model->getBody());
    }
}
