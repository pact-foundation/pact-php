<?php

namespace PhpPactTest\FFI;

use FFI\CData;
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
        $this->assertNotEmpty($result);
    }

    public function testNewMessageInteraction(): void
    {
        $result = $this->client->newMessageInteraction(1, 'test');
        $this->assertNotEmpty($result);
    }

    public function testNewSyncMessageInteraction(): void
    {
        $result = $this->client->newSyncMessageInteraction(1, 'test');
        $this->assertNotEmpty($result);
    }

    public function testGiven(): void
    {
        $result = $this->client->given(1, 'test');
        $this->assertFalse($result);
    }

    public function testGivenWithParam(): void
    {
        $result = $this->client->givenWithParam(1, 'test', 'key', 'value');
        $this->assertFalse($result);
    }

    public function testUponReceiving(): void
    {
        $result = $this->client->uponReceiving(1, 'test');
        $this->assertFalse($result);
    }

    public function testMessageExpectsToReceive(): void
    {
        $this->client->messageExpectsToReceive(1, 'test');
        $this->expectNotToPerformAssertions();
    }

    public function testMessageWithMetadataV2(): void
    {
        $this->client->messageWithMetadataV2(1, 'key', 'value');
        $this->expectNotToPerformAssertions();
    }

    public function testMessageGiven(): void
    {
        $this->client->messageGiven(1, 'test');
        $this->expectNotToPerformAssertions();
    }

    public function testMessageGivenWithParam(): void
    {
        $this->client->messageGivenWithParam(1, 'test', 'key', 'value');
        $this->expectNotToPerformAssertions();
    }

    public function testFreePactHandle(): void
    {
        $result = $this->client->freePactHandle(1);
        $this->assertSame(1, $result);
    }

    public function testNewPact(): void
    {
        $result = $this->client->newPact('consumer', 'provider');
        $this->assertNotEmpty($result);
        $this->client->freePactHandle($result);
    }

    public function testWithSpecification(): void
    {
        $result = $this->client->withSpecification(1, 123);
        $this->assertFalse($result);
    }

    public function testInitWithLogLevel(): void
    {
        $this->client->initWithLogLevel('test');
        $this->expectNotToPerformAssertions();
    }

    public function testPactHandleWriteFile(): void
    {
        $result = $this->client->pactHandleWriteFile(1, 'test', true);
        $this->assertSame(3, $result);
    }

    public function testCleanupPlugins(): void
    {
        $this->client->cleanupPlugins(1);
        $this->expectNotToPerformAssertions();
    }

    public function testUsingPlugin(): void
    {
        putenv('PACT_DO_NOT_TRACK=true');
        putenv(sprintf('PACT_PLUGIN_DIR=%s', __DIR__ . '/../../_resources/plugins'));
        $result = $this->client->usingPlugin(1, 'test', null);
        $this->assertSame(2, $result);
    }

    public function testCleanupMockServer(): void
    {
        $result = $this->client->cleanupMockServer(1234);
        $this->assertFalse($result);
    }

    public function testMockServerMatched(): void
    {
        $result = $this->client->mockServerMatched(1234);
        $this->assertFalse($result);
    }

    public function testMockServerMismatches(): void
    {
        $result = $this->client->mockServerMismatches(1234);
        $this->assertSame('', $result);
    }

    public function testWritePactFile(): void
    {
        $result = $this->client->writePactFile(1234, 'test', true);
        $this->assertSame(3, $result);
    }

    public function testCreateMockServerForTransport(): void
    {
        $result = $this->client->createMockServerForTransport(1, 'localhost', 1234, 'http', null);
        $this->assertSame(-1, $result);
    }

    public function testVerifierNewForApplication(): void
    {
        $result = $this->client->verifierNewForApplication('name', '1.1');
        $this->assertInstanceOf(CData::class, $result);
    }

    public function testVerifierSetProviderInfo(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierSetProviderInfo($handle, null, null, null, null, null);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierAddProviderTransport(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierAddProviderTransport($handle, null, null, null, null);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierSetFilterInfo(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierSetFilterInfo($handle, null, null, true);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierSetProviderState(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierSetProviderState($handle, null, true, true);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierSetVerificationOptions(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $result = $this->client->verifierSetVerificationOptions($handle, true, 1);
            $this->assertSame(0, $result);
        }
    }

    public function testVerifierSetPublishOptions(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $result = $this->client->verifierSetPublishOptions($handle, '1.0.0', null, null, 'some-branch');
            $this->assertSame(0, $result);
        }
    }

    public function testVerifierSetConsumerFilters(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierSetConsumerFilters($handle, null);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierAddCustomHeader(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierAddCustomHeader($handle, 'name', 'value');
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierAddFileSource(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierAddFileSource($handle, '/path/to/file');
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierAddDirectorySource(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierAddDirectorySource($handle, '/path/to/directory');
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierAddUrlSource(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierAddUrlSource($handle, 'http://example.domain/file.ext', null, null, null);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierBrokerSourceWithSelectors(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierBrokerSourceWithSelectors(
                $handle,
                'http://example.domain/file.ext',
                null,
                null,
                null,
                true,
                null,
                null,
                null,
                null,
                null
            );
        }
        $this->expectNotToPerformAssertions();
    }

    public function testVerifierExecute(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $result = $this->client->verifierExecute($handle);
            $this->assertSame(0, $result);
        }
    }

    public function testVerifierJson(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $result = $this->client->verifierJson($handle);
            $this->assertSame('{"errors":[],"interactionResults":[],"notices":[],"output":[],"pendingErrors":[],"result":true}', $result);
        }
    }

    public function testVerifierShutdown(): void
    {
        $handle = $this->client->verifierNewForApplication('name', '1.1');
        if ($handle) {
            $this->client->verifierShutdown($handle);
        }
        $this->expectNotToPerformAssertions();
    }

    public function testMessageReify(): void
    {
        $result = $this->client->messageReify(1);
        $this->assertSame('', $result);
    }

    public function testWithHeaderV2(): void
    {
        $result = $this->client->withHeaderV2(1, 2, '', 0, null);
        $this->assertFalse($result);
    }

    public function testWithQueryParameterV2(): void
    {
        $result = $this->client->withQueryParameterV2(1, '', 0, null);
        $this->assertFalse($result);
    }

    public function testWithRequest(): void
    {
        $result = $this->client->withRequest(1, null, null);
        $this->assertFalse($result);
    }

    public function testResponseStatusV2(): void
    {
        $result = $this->client->responseStatusV2(1, null);
        $this->assertFalse($result);
    }

    public function testInteractionContents(): void
    {
        $result = $this->client->interactionContents(1, 2, 'text/plain', 'abc');
        $this->assertSame(5, $result);
    }

    public function testLoggerInit(): void
    {
        $this->client->loggerInit();
        $this->expectNotToPerformAssertions();
    }

    public function testLoggerAttachSink(): void
    {
        $result = $this->client->loggerAttachSink('stdout', 0);
        $this->assertSame(0, $result);
    }

    public function testLoggerApply(): void
    {
        $result = $this->client->loggerApply();
        $this->assertSame(-1, $result);
    }

    public function testFetchLogBuffer(): void
    {
        $result = $this->client->fetchLogBuffer();
        $this->assertSame('', $result);
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

    public function testGetLevelFilterTrace(): void
    {
        $this->assertSame(5, $this->client->getLevelFilterTrace());
    }

    public function testGetLevelFilterDebug(): void
    {
        $this->assertSame(4, $this->client->getLevelFilterDebug());
    }

    public function testGetLevelFilterInfo(): void
    {
        $this->assertSame(3, $this->client->getLevelFilterInfo());
    }

    public function testGetLevelFilterWarn(): void
    {
        $this->assertSame(2, $this->client->getLevelFilterWarn());
    }

    public function testGetLevelFilterError(): void
    {
        $this->assertSame(1, $this->client->getLevelFilterError());
    }

    public function testGetLevelFilterOff(): void
    {
        $this->assertSame(0, $this->client->getLevelFilterOff());
    }
}
