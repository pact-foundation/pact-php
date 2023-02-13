<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\ProviderResponse;
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

        $data = \json_decode(\json_encode($model->jsonSerialize()), true);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('application/json', $data['headers']['Content-Type']);
        $this->assertEquals('Austin', $data['body']['currentCity']);
    }
}
