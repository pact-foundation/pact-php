<?php

namespace PhpPactTest\FFI;

use PhpPact\FFI\Client;
use PhpPact\FFI\ClientInterface;
use PhpPact\FFI\Model\BinaryData;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private ClientInterface $client;

    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function testWithBinaryFile(): void
    {
        $result = $this->client->withBinaryFile(1, 2, 'image/png', BinaryData::createFrom('hello'));
        $this->assertFalse($result);
    }

    public function testWithMultipartFileV2(): void
    {
        $result = $this->client->withMultipartFileV2(1, 2, 'text/plain', './path/to/file.txt', 'text', 'abc123');
        $this->assertFalse($result->success);
        $this->assertSame('with_multipart_file: Interaction handle is invalid', $result->message);
    }

    public function testGetInteractionPartRequest(): void
    {
        $this->assertSame(0, $this->client->getInteractionPartRequest());
    }

    public function testGetInteractionPartResponse(): void
    {
        $this->assertSame(1, $this->client->getInteractionPartResponse());
    }

    public function testGet(): void
    {
        $this->assertSame(5, $this->client->get('LevelFilter_Trace'));
    }

    #[TestWith(['abc123', true])]
    #[TestWith(['testing', false])]
    public function testCall(string $example, bool $result): void
    {
        $this->assertSame($result, $this->client->call('pactffi_check_regex', '\w{3}\d+', $example));
    }
}
