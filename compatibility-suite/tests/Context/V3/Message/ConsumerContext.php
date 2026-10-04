<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3\Message;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Exception;
use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\Pact\Pact as PactFile;
use PhpPactTest\CompatibilitySuite\Model\Pact\ProviderState;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\ReceivedMessage;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\BodyValidatorInterface;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\MessageGeneratorBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ParserInterface;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private MessageBuilder $builder;
    private ReceivedMessage|null $receivedMessage;
    private bool $verifyResult;
    private PactFile $pact;
    private PactPath $pactPath;

    public function __construct(
        string $specificationVersion,
        private MessageGeneratorBuilderInterface $messageGeneratorBuilder,
        private ParserInterface $parser,
        private BodyValidatorInterface $validator,
        private BodyStorageInterface $bodyStorage,
        private FixtureLoaderInterface $fixtureLoader
    ) {
        $this->pactPath = new PactPath(sprintf('message_consumer_specification_%s', $specificationVersion));
        $config = new PactMessageConfig();
        $config
            ->setConsumer($this->pactPath->getConsumer())
            ->setProvider(PactPath::PROVIDER)
            ->setPactDir(Path::PACTS_PATH)
            ->setPactSpecificationVersion($specificationVersion)
            ->setPactFileWriteMode(WriteMode::OVERWRITE);
        $this->builder = new MessageBuilder($config);
    }

    #[Given('a message integration is being defined for a consumer test')]
    public function aMessageIntegrationIsBeingDefinedForAConsumerTest(): void
    {
        $this->builder->expectsToReceive('a message');
    }

    #[Given('the message payload contains the :fixture JSON document')]
    public function theMessagePayloadContainsTheJsonDocument(string $fixture): void
    {
        $this->builder->withContent($this->parser->parseBody('file:' . $fixture . '.json'));
    }

    #[When('the message is successfully processed')]
    public function theMessageIsSuccessfullyProcessed(): void
    {
        $this->process([$this, 'storeMessage']);
    }

    #[Then('the received message payload will contain the :fixture JSON document')]
    public function theReceivedMessagePayloadWillContainTheJsonDocument(string $fixture): void
    {
        if (null === $this->receivedMessage) {
            throw new Exception('The received message is null.');
        }
        Assert::assertJsonStringEqualsJsonString(
            $this->fixtureLoader->load($fixture . '.json'),
            TypeCaster::toString(json_encode($this->receivedMessage->getContents()))
        );
    }

    #[Then('the received message content type will be :contentType')]
    public function theReceivedMessageContentTypeWillBe(string $contentType): void
    {
        if (null === $this->receivedMessage) {
            throw new Exception('The received message is null.');
        }
        Assert::assertSame($contentType, $this->receivedMessage->getContentType());
    }

    #[Then('the consumer test will have passed')]
    public function theConsumerTestWillHavePassed(): void
    {
        Assert::assertTrue($this->verifyResult);
    }

    #[Then('a Pact file for the message interaction will have been written')]
    public function aPactFileForTheMessageInteractionWillHaveBeenWritten(): void
    {
        Assert::assertTrue(file_exists($this->pactPath));
        $this->pact = PactFile::fromFile($this->pactPath);
    }

    #[Then('the pact file will contain :messages message interaction(s)')]
    public function thePactFileWillContainMessageInteraction(int $messages): void
    {
        Assert::assertCount($messages, $this->pact->getMessages());
    }

    #[Then('the first message in the pact file will contain the :fixture document')]
    public function theFirstMessageInThePactFileWillContainTheDocument(string $fixture): void
    {
        Assert::assertJsonStringEqualsJsonString(
            $this->fixtureLoader->load($fixture),
            TypeCaster::toString(json_encode($this->pact->getMessage(0)->getContents()))
        );
    }

    #[Then('the first message in the pact file content type will be :contentType')]
    public function theFirstMessageInThePactFileContentTypeWillBe(string $contentType): void
    {
        Assert::assertSame($contentType, TypeCaster::toString($this->pact->getMessage(0)->getMetadata()['contentType'] ?? ''));
    }

    #[When('the message is NOT successfully processed with a :error exception')]
    public function theMessageIsNotSuccessfullyProcessedWithAException(string $error): void
    {
        $this->process(fn () => throw new Exception($error));
    }

    #[Then('the consumer test will have failed')]
    public function theConsumerTestWillHaveFailed(): void
    {
        Assert::assertFalse($this->verifyResult);
    }

    #[Then('the consumer test error will be :error')]
    public function theConsumerTestErrorWillBe(string $error): void
    {
        // TODO Modify MessageBuilder code to check this exception?
    }

    #[Then('a Pact file for the message interaction will NOT have been written')]
    public function aPactFileForTheMessageInteractionWillNotHaveBeenWritten(): void
    {
        Assert::assertFalse(file_exists($this->pactPath));
    }

    #[Given('the message contains the following metadata:')]
    public function theMessageContainsTheFollowingMetadata(TableNode $table): void
    {
        $this->builder->withMetadata($this->parser->parseMetadataTable($table->getHash()));
    }

    #[Then('/^the received message metadata will contain "([^"]+)" == "(.+)"$/')]
    public function theReceivedMessageMetadataWillContain(string $key, string $value): void
    {
        if (null === $this->receivedMessage) {
            throw new Exception('The received message is null.');
        }
        $metadata = $this->receivedMessage->getMetadata();
        $actual = $metadata[$key] ?? null;
        if (is_string($actual)) {
            Assert::assertSame($this->parser->parseMetadataValue($value), $actual);
        } else {
            Assert::assertJsonStringEqualsJsonString($this->parser->parseMetadataValue($value), TypeCaster::toString(json_encode($actual)));
        }
    }

    #[Then('/^the first message in the pact file will contain the message metadata "([^"]+)" == "(.+)"$/')]
    public function theFirstMessageInThePactFileWillContainTheMessageMetadata(string $key, string $value): void
    {
        $actual = $this->pact->getMessage(0)->getMetadata()[$key] ?? null;
        if (is_string($actual)) {
            Assert::assertSame($this->parser->parseMetadataValue($value), $actual);
        } else {
            Assert::assertJsonStringEqualsJsonString($this->parser->parseMetadataValue($value), TypeCaster::toString(json_encode($actual)));
        }
    }

    #[Given('a provider state :state for the message is specified')]
    public function aProviderStateForTheMessageIsSpecified(string $state): void
    {
        $this->builder->given($state, []);
    }

    #[Given('a message is defined')]
    public function aMessageIsDefined(): void
    {
        $this->aMessageIntegrationIsBeingDefinedForAConsumerTest();
    }

    #[Then('the first message in the pact file will contain :states provider state(s)')]
    public function theFirstMessageInThePactFileWillContainProviderStates(int $states): void
    {
        Assert::assertCount($states, $this->pact->getMessage(0)->getProviderStates());
    }

    #[Then('the first message in the Pact file will contain provider state :state')]
    public function theFirstMessageInThePactFileWillContainProviderState(string $state): void
    {
        $states = array_map(fn (ProviderState $state): string => $state->getName(), $this->pact->getMessage(0)->getProviderStates());
        Assert::assertContains($state, $states);
    }

    #[Given('a provider state :state for the message is specified with the following data:')]
    public function aProviderStateForTheMessageIsSpecifiedWithTheFollowingData(string $state, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows) ?: [];
        $this->builder->given($state, $row);
    }

    #[Then('the provider state :state for the message will contain the following parameters:')]
    public function theProviderStateForTheMessageWillContainTheFollowingParameters(string $state, TableNode $table): void
    {
        $params = json_decode($table->getHash()[0]['parameters'], true);
        Assert::assertContains([
            'name' => $state,
            'params' => $params,
        ], array_map(fn (ProviderState $state): array => $state->toArray(), $this->pact->getMessage(0)->getProviderStates()));
    }

    #[Given('the message is configured with the following:')]
    public function theMessageIsConfiguredWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows) ?: [];
        $message = new Message();
        $message->setBody(isset($row['body']) ? $this->parser->parseBody($row['body']) : null);
        $metadata = null;
        if (isset($row['metadata'])) {
            $metadata = [];
            foreach ((array) json_decode($row['metadata'], true) as $key => $value) {
                $metadata[(string) $key] = is_string($value) ? $value : TypeCaster::toString(json_encode($value));
            }
        }
        $message->setMetadata($metadata);
        $this->messageGeneratorBuilder->build($message, $row['generators']);
        if ($message->hasBody()) {
            $this->builder->withContent($message->getBody());
        }
        if ($message->hasMetadata()) {
            $this->builder->withContent('not empty'); // any not empty text, doesn't matter. If empty or not provided, received message will be null.
            $this->builder->withMetadata($message->getMetadata() ?? []);
        }
    }

    #[Then('the message contents for :path will have been replaced with a(n) :type')]
    public function theMessageContentsForWillHaveBeenReplacedWithAn(string $path, string $type): void
    {
        if (null === $this->receivedMessage) {
            throw new Exception('The received message is null.');
        }
        $this->bodyStorage->setBody(TypeCaster::toString(json_encode($this->receivedMessage->getContents())));
        $this->validator->validateType($path, $type);
    }

    #[Then('the received message metadata will contain :key replaced with an :type')]
    public function theReceivedMessageMetadataWillContainReplacedWithAn(string $key, string $type): void
    {
        if (null === $this->receivedMessage) {
            throw new Exception('The received message is null.');
        }
        $this->bodyStorage->setBody(TypeCaster::toString(json_encode($this->receivedMessage->getMetadata())));
        $this->validator->validateType("$.$key", $type);
    }

    public function storeMessage(string $message): void
    {
        $this->receivedMessage = ReceivedMessage::fromJson($message);
    }

    private function process(callable $callback): void
    {
        $this->builder->setCallback($callback);

        $this->verifyResult = $this->builder->verify();
    }
}
