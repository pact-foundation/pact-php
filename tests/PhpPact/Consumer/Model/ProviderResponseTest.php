<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ProviderResponse;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ProviderResponseTest extends TestCase
{
    private ProviderResponse $response;

    public function setUp(): void
    {
        $this->response = new ProviderResponse();
    }

    public function testSerializing(): void
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

    #[TestWith([null])]
    #[TestWith([new Text('column1,column2,column3', 'text/csv')])]
    #[TestWith([new Binary('/path/to/image.png', 'image/png')])]
    #[TestWith([new Multipart([], 'abc123')])]
    public function testBody(mixed $body): void
    {
        $this->assertSame($this->response, $this->response->setBody($body));
        $this->assertSame($body, $this->response->getBody());
    }

    public function testTextBody(): void
    {
        $text = 'example text';
        $this->assertSame($this->response, $this->response->setBody($text));
        $body = $this->response->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertSame($text, $body->getContents());
        $this->assertSame('text/plain', $body->getContentType());
    }

    public function testJsonBody(): void
    {
        $array = ['key' => 'value'];
        $this->assertSame($this->response, $this->response->setBody($array));
        $body = $this->response->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertSame('{"key":"value"}', $body->getContents());
        $this->assertSame('application/json', $body->getContentType());
    }
}
