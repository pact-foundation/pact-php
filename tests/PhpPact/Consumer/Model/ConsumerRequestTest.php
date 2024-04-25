<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Multipart;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ConsumerRequestTest extends TestCase
{
    private ConsumerRequest $request;

    public function setUp(): void
    {
        $this->request = new ConsumerRequest();
    }

    public function testSerializing(): void
    {
        $model = new ConsumerRequest();
        $model
            ->setMethod('PUT')
            ->setPath('/somepath')
            ->addHeader('Content-Type', 'application/json')
            ->addQueryParameter('fruit', ['apple', 'banana'])
            ->setBody([
                'currentCity' => 'Austin',
            ]);

        $this->assertEquals('PUT', $model->getMethod());
        $this->assertEquals(['Content-Type' => ['application/json']], $model->getHeaders());
        $this->assertEquals(['fruit' => ['apple', 'banana']], $model->getQuery());
        $this->assertEquals('/somepath', $model->getPath());

        $body = $model->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertEquals('{"currentCity":"Austin"}', $body->getContents());
        $this->assertEquals('application/json', $body->getContentType());
    }

    public function testSerializingWhenPathUsingMatcher(): void
    {
        $matcher = new Matcher();
        $pathVariable = '474d610b-c6e3-45bd-9f70-529e7ad21df0';
        $model = new ConsumerRequest();
        $model
            ->setMethod('PATCH')
            ->setPath($matcher->regex("/somepath/$pathVariable/status", '\/somepath\/[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}\/status'))
            ->addHeader('Content-Type', 'application/json')
            ->addQueryParameter('food', 'milk')
            ->setBody([
                'status' => 'finished',
            ]);

        $this->assertEquals('PATCH', $model->getMethod());
        $this->assertEquals(['Content-Type' => ['application/json']], $model->getHeaders());
        $this->assertEquals(['food' => ['milk']], $model->getQuery());
        $this->assertJsonStringEqualsJsonString('{"value":"\/somepath\/474d610b-c6e3-45bd-9f70-529e7ad21df0\/status","regex":"\\\\\\/somepath\\\\\\/[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}\\\\\\/status","pact:matcher:type":"regex"}', $model->getPath());

        $body = $model->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertEquals('{"status":"finished"}', $body->getContents());
        $this->assertEquals('application/json', $body->getContentType());
    }

    #[TestWith([null])]
    #[TestWith([new Text('column1,column2,column3', 'text/csv')])]
    #[TestWith([new Binary('/path/to/image.png', 'image/png')])]
    #[TestWith([new Multipart([], 'abc123')])]
    public function testBody(mixed $body): void
    {
        $this->assertSame($this->request, $this->request->setBody($body));
        $this->assertSame($body, $this->request->getBody());
    }

    public function testTextBody(): void
    {
        $text = 'example text';
        $this->assertSame($this->request, $this->request->setBody($text));
        $body = $this->request->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertSame($text, $body->getContents());
        $this->assertSame('text/plain', $body->getContentType());
    }

    public function testJsonBody(): void
    {
        $array = ['key' => 'value'];
        $this->assertSame($this->request, $this->request->setBody($array));
        $body = $this->request->getBody();
        $this->assertInstanceOf(Text::class, $body);
        $this->assertSame('{"key":"value"}', $body->getContents());
        $this->assertSame('application/json', $body->getContentType());
    }
}
