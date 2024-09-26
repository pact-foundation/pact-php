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

    public function testWithBody(): void
    {
        $result = $this->client->withBody(1, 2, 'text/plain', 'test');
        $this->assertFalse($result);
    }

    public function testWithMultipartFileV2(): void
    {
        $result = $this->client->withMultipartFileV2(1, 2, 'text/plain', './path/to/file.txt', 'text', 'abc123');
        $this->assertFalse($result->success);
        $this->assertSame('with_multipart_file: Interaction handle is invalid', $result->message);
    }

    public function testSetKey(): void
    {
        $result = $this->client->setKey(1, 'test');
        $this->assertFalse($result);
    }

    public function testSetPending(): void
    {
        $result = $this->client->setPending(1, true);
        $this->assertFalse($result);
    }

    public function testSetComment(): void
    {
        $result = $this->client->setComment(1, 'key', 'value');
        $this->assertFalse($result);
    }

    public function testAddTextComment(): void
    {
        $result = $this->client->addTextComment(1, 'test');
        $this->assertFalse($result);
    }

    public function testNewInteraction(): void
    {
        $result = $this->client->newInteraction(1, 'test');
        $this->assertIsInt($result);
    }

    public function testNewMessageInteraction(): void
    {
        $result = $this->client->newMessageInteraction(1, 'test');
        $this->assertIsInt($result);
    }

    public function testNewSyncMessageInteraction(): void
    {
        $result = $this->client->newSyncMessageInteraction(1, 'test');
        $this->assertIsInt($result);
    }

    public function testGetInteractionPartRequest(): void
    {
        $this->assertSame(0, $this->client->getInteractionPartRequest());
    }

    public function testGetInteractionPartResponse(): void
    {
        $this->assertSame(1, $this->client->getInteractionPartResponse());
    }

    public function testGetPactSpecificationV1(): void
    {
        $this->assertSame(1, $this->client->getPactSpecificationV1());
    }

    public function testGetPactSpecificationV1_1(): void
    {
        $this->assertSame(2, $this->client->getPactSpecificationV1_1());
    }

    public function testGetPactSpecificationV2(): void
    {
        $this->assertSame(3, $this->client->getPactSpecificationV2());
    }

    public function testGetPactSpecificationV3(): void
    {
        $this->assertSame(4, $this->client->getPactSpecificationV3());
    }

    public function testGetPactSpecificationV4(): void
    {
        $this->assertSame(5, $this->client->getPactSpecificationV4());
    }

    public function testGetPactSpecificationUnknown(): void
    {
        $this->assertSame(0, $this->client->getPactSpecificationUnknown());
    }

    #[TestWith(['abc123', true])]
    #[TestWith(['testing', false])]
    public function testCall(string $example, bool $result): void
    {
        $this->assertSame($result, $this->client->call('pactffi_check_regex', '\w{3}\d+', $example));
    }
}
