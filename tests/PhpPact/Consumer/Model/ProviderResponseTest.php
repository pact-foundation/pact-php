<?php

namespace PhpPact\Consumer\Model;

use PHPUnit\Framework\TestCase;

class ProviderResponseTest extends TestCase
{
    public function testSerializing()
    {
        $model = new ProviderResponse();
        $model
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'currentCity' => 'Austin',
            ]);

        $this->assertEquals(200, $model->getStatus());
        $this->assertEquals(['Content-Type' => ['application/json']], $model->getHeaders());
        $this->assertEquals('{"currentCity":"Austin"}', $model->getBody());
    }
}
