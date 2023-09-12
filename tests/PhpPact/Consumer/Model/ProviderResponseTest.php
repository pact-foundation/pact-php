<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\Body\Text;
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

        $this->assertEquals(200, $model->getStatus());
        $this->assertEquals(['Content-Type' => ['application/json']], $model->getHeaders());

        $body = $model->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertEquals('{"currentCity":"Austin"}', $body->getContents());
        $this->assertEquals('application/json', $body->getContentType());
    }
}
