<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\SyncMessage;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\Standalone\MockService\Model\VerifyResult;
use PhpPact\SyncMessage\Driver\Interaction\SyncMessageDriverInterface;
use PhpPact\SyncMessage\Model\SyncMessage;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\Xml;
use PhpPactTest\CompatibilitySuite\Plugin\SyncMessageTestDriverFactory;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\BodyValidatorInterface;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\ParserInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private SyncMessage $message;
    private array $pact;
    private PactPath $pactPath;
    private MockServerConfig $config;
    private SyncMessageDriverInterface $driver;
    private string $resp;
    private ?VerifyResult $verifyResult = null;
    private string $generatedRequestContent = '';
    private string $generatedResponseContent = '';
    public function __construct(
        private string $specificationVersion,
        private FixtureLoaderInterface $fixtureLoader,
        private ParserInterface $parser,
        private BodyValidatorInterface $validator,
        private BodyStorageInterface $bodyStorage
    ) {
        $this->pactPath = new PactPath();
        $this->config = new MockServerConfig();
        $this->config
            ->setHost('127.0.0.1')
            ->setConsumer($this->pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($this->specificationVersion)
            ->setPactFileWriteMode(WriteMode::OVERWRITE);
        $this->driver = (new SyncMessageTestDriverFactory())->create($this->config);
    }

    #[Given('a synchronous message interaction is being defined for a consumer test')]
    public function aSynchronousMessageInteractionIsBeingDefinedForAConsumerTest(): void
    {
        $this->message = new SyncMessage();
        $this->message->setDescription('a synchronous message');
    }

    #[When('the Pact file for the test is generated')]
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->driver->registerMessage($this->message);
        $this->driver->writePactAndCleanUp();
    }

    #[Then('the first interaction in the Pact file will have a type of :type')]
    public function theFirstInteractionInThePactFileWillHaveATypeOf(string $type): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertSame($type, $pact['interactions'][0]['type']);
    }

    #[Given('a key of :key is specified for the synchronous message interaction')]
    public function aKeyOfIsSpecifiedForTheSynchronousMessageInteraction(string $key): void
    {
        $this->message->setKey($key);
    }

    #[Given('the synchronous message interaction is marked as pending')]
    public function theSynchronousMessageInteractionIsMarkedAsPending(): void
    {
        $this->message->setPending(true);
    }

    #[Given('a comment :value is added to the synchronous message interaction')]
    public function aCommentIsAddedToTheSynchronousMessageInteraction(string $value): void
    {
        $this->message->addTextComment($value);
    }

    #[Then('the first interaction in the Pact file will have :name = :value')]
    public function theFirstInteractionInThePactFileWillHave(string $name, string $value): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertJsonStringEqualsJsonString($value, json_encode($pact['interactions'][0][$name]));
    }

    #[Given('the message request payload contains the :fixture JSON document')]
    public function theMessageRequestPayloadContainsTheJsonDocument(string $fixture): void
    {
        $this->message->setRequestContents($this->fixtureLoader->loadJson($fixture . '.json'));
    }

    #[Given('the message response payload contains the :fixture document')]
    public function theMessageResponsePayloadContainsTheDocument(string $fixture): void
    {
        $body = $this->parser->parseBody($fixture);
        if ($body instanceof Xml) {
            $body = new Text($body->getRawContents(), 'application/xml');
        }
        $this->message->addResponseContents($body);
    }

    #[Then('the received message payload will contain the :fixture document')]
    public function theReceivedMessagePayloadWillContainTheDocument(string $fixture): void
    {
        $expectedBody = $this->parser->parseBody($fixture);
        $expectedContent = $expectedBody->getContents();
        if ($expectedBody instanceof Xml) {
            $expectedContent = $expectedBody->getRawContents();
        }
        Assert::assertStringContainsString('response-1-content: ' . $expectedContent, $this->resp);
    }

    #[Then('a Pact file for the message interaction will have been written')]
    public function aPactFileForTheMessageInteractionWillHaveBeenWritten(): void
    {
        Assert::assertTrue(file_exists($this->pactPath));
        $this->pact = json_decode(file_get_contents($this->pactPath), true);
    }

    #[Then('the pact file will contain :num interaction')]
    public function thePactFileWillContainInteraction(int $num): void
    {
        Assert::assertCount($num, $this->pact['interactions']);
    }

    #[Then('the first interaction in the pact file will contain the :fixture document as the request')]
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheRequest(string $fixture): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $expectedBody = $this->parser->parseBody($fixture);
        $expectedContent = $expectedBody->getContents();
        if ($expectedBody instanceof Xml) {
            $expectedContent = $expectedBody->getRawContents();
        } elseif ($expectedBody instanceof Text && $expectedBody->getContentType() === 'application/json') {
            $expectedContent = json_decode($expectedBody->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }
        Assert::assertEquals($expectedContent, $pact['interactions'][0]['request']['contents']['content'] ?? null);
    }

    #[Then('the first interaction in the pact file request content type will be :contentType')]
    public function theFirstInteractionInThePactFileRequestContentTypeWillBe(string $contentType): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertSame($contentType, $pact['interactions'][0]['request']['contents']['contentType']);
    }

    #[Then('the first interaction in the pact file will contain the :fixture document as a response')]
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsAResponse(string $fixture): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $expectedBody = $this->parser->parseBody($fixture);
        $expectedContent = $expectedBody->getContents();
        if ($expectedBody instanceof Xml) {
            $expectedContent = $expectedBody->getRawContents();
        } elseif ($expectedBody instanceof Text && $expectedBody->getContentType() === 'application/json') {
            $expectedContent = json_decode($expectedBody->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }
        Assert::assertEquals($expectedContent, $pact['interactions'][0]['response'][0]['contents']['content'] ?? null);
    }

    #[Then('the first interaction in the pact file response content type will be :contentType')]
    public function theFirstInteractionInThePactFileResponseContentTypeWillBe(string $contentType): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertSame($contentType, $pact['interactions'][0]['response'][0]['contents']['contentType']);
    }

    #[Then('the first interaction in the pact file will contain :num response messages')]
    public function theFirstInteractionInThePactFileWillContainResponseMessages(int $num): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertCount($num, $pact['interactions'][0]['response']);
    }

    #[Then('the first interaction in the pact file will contain the :fixture document as the first response message')]
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheFirstResponseMessage(string $fixture): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $expectedBody = $this->parser->parseBody($fixture);
        $expectedContent = $expectedBody->getContents();
        if ($expectedBody instanceof Xml) {
            $expectedContent = $expectedBody->getRawContents();
        } elseif ($expectedBody instanceof Text && $expectedBody->getContentType() === 'application/json') {
            $expectedContent = json_decode($expectedBody->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }
        Assert::assertEquals($expectedContent, $pact['interactions'][0]['response'][0]['contents']['content'] ?? null);
    }

    #[Then('the first interaction in the pact file will contain the :fixture document as the second response message')]
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheSecondResponseMessage(string $fixture): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $expectedBody = $this->parser->parseBody($fixture);
        $expectedContent = $expectedBody->getContents();
        if ($expectedBody instanceof Xml) {
            $expectedContent = $expectedBody->getRawContents();
        } elseif ($expectedBody instanceof Text && $expectedBody->getContentType() === 'application/json') {
            $expectedContent = json_decode($expectedBody->getContents(), true, 512, JSON_THROW_ON_ERROR);
        }
        Assert::assertEquals($expectedContent, $pact['interactions'][0]['response'][1]['contents']['content'] ?? null);
    }

    #[Given('the message request contains the following metadata:')]
    public function theMessageRequestContainsTheFollowingMetadata(TableNode $table): void
    {
        $this->message->setRequestMetadata($this->parser->parseMetadataTable($table->getHash()));
    }

    #[Then('/^the received message request metadata will contain "([^"]+)" == "(.+)"$/')]
    public function theReceivedMessageRequestMetadataWillContain(string $key, string $value): void
    {
        if (preg_match('/^request-metadata: (.+)$/m', $this->resp, $matches)) {
            $json = $matches[1];
        } else {
            $json = '{}';
        }
        $data = json_decode($json, true);

        // Parse expected value: if it starts with "JSON: ", parse it as JSON
        $expectedValue = $value;
        if (str_starts_with($value, 'JSON: ')) {
            $expectedValue = stripslashes(substr($value, 6));
            $decoded = json_decode($expectedValue, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Compare as arrays/objects
                Assert::assertEquals($decoded, $data[$key]);
                return;
            }
        }

        Assert::assertEquals($expectedValue, $data[$key]);
    }

    #[Then('/^the first message in the pact file will contain the request message metadata "([^"]+)" == "(.+)"$/')]
    public function theFirstMessageInThePactFileWillContainTheRequestMessageMetadata(string $key, string $value): void
    {
        // Parse expected value: if it starts with "JSON: ", parse it as JSON and re-serialize
        $expectedValue = $value;
        if (str_starts_with($value, 'JSON: ')) {
            $expectedValue = stripslashes(substr($value, 6));
            $expectedValue = json_decode($expectedValue, true);
        }
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertEquals($expectedValue, $pact['interactions'][0]['request']['metadata'][$key] ?? null);
    }

    #[Given('a provider state :state for the synchronous message is specified')]
    public function aProviderStateForTheSynchronousMessageIsSpecified(string $state): void
    {
        $this->message->addProviderState($state, []);
    }

    #[Given('a provider state :state for the synchronous message is specified with the following data:')]
    public function aProviderStateForTheSynchronousMessageIsSpecifiedWithTheFollowingData(string $state, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $this->message->addProviderState($state, $row);
    }

    #[Then('the first message in the pact file will contain :states provider state(s)')]
    public function theFirstMessageInThePactFileWillContainProviderStates(int $states): void
    {
        Assert::assertCount($states, $this->pact['interactions'][0]['providerStates'] ?? []);
    }

    #[Then('the first message in the Pact file will contain provider state :state')]
    public function theFirstMessageInThePactFileWillContainProviderState(string $state): void
    {
        $states = array_map(fn (array $state): string => $state['name'], $this->pact['interactions'][0]['providerStates']);
        Assert::assertContains($state, $states);
    }

    #[Then('the provider state :state for the message will contain the following parameters:')]
    public function theProviderStateForTheMessageWillContainTheFollowingParameters(string $state, TableNode $table): void
    {
        $params = json_decode($table->getHash()[0]['parameters'], true);
        Assert::assertContains([
            'name' => $state,
            'params' => $params,
        ], $this->pact['interactions'][0]['providerStates']);
    }

    #[Given('the message request is configured with the following:')]
    public function theMessageRequestIsConfiguredWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        if (isset($row['body'])) {
            $this->message->setRequestContents($this->parser->parseBody($row['body']));
        }
        if (isset($row['metadata'])) {
            $this->message->setRequestMetadata(json_decode($row['metadata'], true));
        }
        if (isset($row['generators'])) {
            $generators = $row['generators'];
            if (str_starts_with($generators, 'JSON:')) {
                $generators = trim(substr($generators, 5));
            } elseif (str_starts_with($generators, 'file:')) {
                $generators = $this->fixtureLoader->load(trim(substr($generators, 5)));
            } else {
                $generators = $this->fixtureLoader->load($generators);
            }
            $this->message->setRequestGenerators($generators);
        }
    }

    #[Given('the message response is configured with the following:')]
    public function theMessageResponseIsConfiguredWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        if (isset($row['body'])) {
            $this->message->addResponseContents($this->parser->parseBody($row['body']));
        }
        if (isset($row['metadata'])) {
            $this->message->setResponeMetadata(json_decode($row['metadata'], true));
        }
        if (isset($row['generators'])) {
            $generators = $row['generators'];
            if (str_starts_with($generators, 'JSON:')) {
                $generators = trim(substr($generators, 5));
            } elseif (str_starts_with($generators, 'file:')) {
                $generators = $this->fixtureLoader->load(trim(substr($generators, 5)));
            } else {
                $generators = $this->fixtureLoader->load($generators);
            }
            $this->message->setResponseGenerators($generators);
        }
    }

    #[Then('the message request contents for :path will have been replaced with a(n) :type')]
    public function theMessageRequestContentsForWillHaveBeenReplacedWithAn(string $path, string $type): void
    {
        $this->bodyStorage->setBody($this->generatedRequestContent);
        $this->validator->validateType($path, $type);
    }

    #[Then('the message response contents for :path will have been replaced with a(n) :type')]
    public function theMessageResponseContentsForWillHaveBeenReplacedWithAn(string $path, string $type): void
    {
        $this->bodyStorage->setBody($this->generatedResponseContent);
        $this->validator->validateType($path, $type);
    }

    #[Then('the received message request metadata will contain :key replaced with a(n) :type')]
    public function theReceivedMessageRequestMetadataWillContainReplacedWithAn(string $key, string $type): void
    {
        if (preg_match('/^request-metadata: (.+)$/m', $this->resp, $matches)) {
            $this->bodyStorage->setBody($matches[1]);
        }
        $this->validator->validateType('$.' . $key, $type);
    }

    #[Then('the received message response metadata will contain :key == :value')]
    public function theReceivedMessageResponseMetadataWillContain(string $key, string $value): void
    {
        $pattern = '/^response-1-metadata: ' . preg_quote($key, '/') . ': (.+)$/m';
        if (preg_match($pattern, $this->resp, $matches)) {
            $actualValue = $matches[1];
            $expectedValue = $value;
            if (str_starts_with($value, 'JSON: ')) {
                $expectedValue = stripslashes(substr($value, 6));
                $decoded = json_decode($expectedValue, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $expectedValue = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }
            Assert::assertEquals($expectedValue, $actualValue);
        }
    }

    #[Then('the received message response metadata will contain :key replaced with an :type')]
    public function theReceivedMessageResponseMetadataWillContainReplacedWithAn(string $key, string $type): void
    {
        $pattern = '/^response-1-metadata: (.+)$/m';
        if (preg_match($pattern, $this->resp, $matches)) {
            $this->bodyStorage->setBody($matches[1]);
        }
        $this->validator->validateType('$.' . $key, $type);
    }

    #[When('the message is successfully processed')]
    public function theMessageIsSuccessfullyProcessed(): void
    {
        $this->driver->registerMessage($this->message);
        $requestContents = $this->message->getRequestContents();
        $body = match (true) {
            $requestContents instanceof Text => $requestContents->getContents(),
            $requestContents instanceof Binary => file_get_contents($requestContents->getPath()) ?: null,
            is_null($requestContents) => null,
        };
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket) {
            socket_connect($socket, $this->config->getHost(), $this->config->getPort());
            socket_write($socket, $body . "\n");
            $this->resp = socket_read($socket, 65536);
            socket_close($socket);
        }
        if (preg_match('/^request-content: (.+)$/m', $this->resp ?? '', $matches)) {
            $this->generatedRequestContent = $matches[1];
        }
        if (preg_match('/^response-1-content: (.+)$/m', $this->resp ?? '', $matches)) {
            $this->generatedResponseContent = $matches[1];
        }
        if (!file_exists((string) $this->pactPath)) {
            $this->verifyResult = $this->driver->verifyMessage();
        }
    }

    #[Then('the consumer test will have passed')]
    public function theConsumerTestWillHavePassed(): void
    {
        if ($this->verifyResult === null) {
            $this->verifyResult = $this->driver->verifyMessage();
        }
        Assert::assertTrue($this->verifyResult->matched);
    }

    #[Then('the received message content type will be :contentType')]
    public function theReceivedMessageContentTypeWillBe(string $contentType): void
    {
        Assert::assertStringContainsString("response-1-content-type: $contentType", $this->resp);
    }
}
