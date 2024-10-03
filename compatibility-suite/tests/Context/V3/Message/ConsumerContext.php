<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3\Message;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Exception;
use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\MessageBuilder;
use PhpPact\Standalone\PactMessage\PactMessageConfig;
use PhpPactTest\CompatibilitySuite\Constant\Path;
use PhpPactTest\CompatibilitySuite\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\BodyStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\BodyValidatorInterface;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\MessageGeneratorBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ParserInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private MessageBuilder $builder;
    private object|null $receivedMessage;
    private bool $verifyResult;
    private array $pact;
    private PactPath $pactPath;

    public function __construct(
        private string $specificationVersion,
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

    /**
     * @Given a message integration is being defined for a consumer test
     */
    public function aMessageIntegrationIsBeingDefinedForAConsumerTest(): void
    {
        $this->builder->expectsToReceive('a message');
    }

    /**
     * @Given the message payload contains the :fixture JSON document
     */
    public function theMessagePayloadContainsTheJsonDocument(string $fixture): void
    {
        $this->builder->withContent($this->fixtureLoader->loadJson($fixture . '.json'));
    }

    /**
     * @When the message is successfully processed
     */
    public function theMessageIsSuccessfullyProcessed(): void
    {
        $this->process([$this, 'storeMessage']);
    }

    /**
     * @Then the received message payload will contain the :fixture JSON document
     */
    public function theReceivedMessagePayloadWillContainTheJsonDocument(string $fixture): void
    {
        Assert::assertJsonStringEqualsJsonString(
            $this->fixtureLoader->load($fixture . '.json'),
            json_encode($this->receivedMessage->contents)
        );
    }

    /**
     * @Then the received message content type will be :contentType
     */
    public function theReceivedMessageContentTypeWillBe(string $contentType): void
    {
        Assert::assertSame($contentType, $this->receivedMessage->metadata->contentType);
    }

    /**
     * @Then the consumer test will have passed
     */
    public function theConsumerTestWillHavePassed(): void
    {
        Assert::assertTrue($this->verifyResult);
    }

    /**
     * @Then a Pact file for the message interaction will have been written
     */
    public function aPactFileForTheMessageInteractionWillHaveBeenWritten(): void
    {
        Assert::assertTrue(file_exists($this->pactPath));
        $this->pact = json_decode(file_get_contents($this->pactPath), true);
    }

    /**
     * @Then the pact file will contain :messages message interaction(s)
     */
    public function thePactFileWillContainMessageInteraction(int $messages): void
    {
        Assert::assertCount($messages, $this->pact['messages'] ?? []);
    }

    /**
     * @Then the first message in the pact file will contain the :fixture document
     */
    public function theFirstMessageInThePactFileWillContainTheDocument(string $fixture): void
    {
        Assert::assertJsonStringEqualsJsonString(
            $this->fixtureLoader->load($fixture),
            json_encode($this->pact['messages'][0]['contents'] ?? null)
        );
    }

    /**
     * @Then the first message in the pact file content type will be :contentType
     */
    public function theFirstMessageInThePactFileContentTypeWillBe(string $contentType): void
    {
        Assert::assertSame($contentType, $this->pact['messages'][0]['metadata']['contentType'] ?? null);
    }

    /**
     * @When the message is NOT successfully processed with a :error exception
     */
    public function theMessageIsNotSuccessfullyProcessedWithAException(string $error): void
    {
        $this->process(fn () => throw new Exception($error));
    }

    /**
     * @Then the consumer test will have failed
     */
    public function theConsumerTestWillHaveFailed(): void
    {
        Assert::assertFalse($this->verifyResult);
    }

    /**
     * @Then the consumer test error will be :error
     */
    public function theConsumerTestErrorWillBe(string $error): void
    {
        // TODO Modify MessageBuilder code to check this exception?
    }

    /**
     * @Then a Pact file for the message interaction will NOT have been written
     */
    public function aPactFileForTheMessageInteractionWillNotHaveBeenWritten(): void
    {
        Assert::assertFalse(file_exists($this->pactPath));
    }

    /**
     * @Given the message contains the following metadata:
     */
    public function theMessageContainsTheFollowingMetadata(TableNode $table): void
    {
        $this->builder->withMetadata($this->parser->parseMetadataTable($table->getHash()));
    }

    /**
     * @Then /^the received message metadata will contain "([^"]+)" == "(.+)"$/
     */
    public function theReceivedMessageMetadataWillContain(string $key, string $value): void
    {
        $actual = $this->receivedMessage->metadata->{$key};
        if (is_string($actual)) {
            Assert::assertSame($this->parser->parseMetadataValue($value), $actual);
        } else {
            Assert::assertJsonStringEqualsJsonString($this->parser->parseMetadataValue($value), json_encode($actual));
        }
    }

    /**
     * @Then /^the first message in the pact file will contain the message metadata "([^"]+)" == "(.+)"$/
     */
    public function theFirstMessageInThePactFileWillContainTheMessageMetadata(string $key, string $value): void
    {
        $actual = $this->pact['messages'][0]['metadata'][$key] ?? null;
        if (is_string($actual)) {
            Assert::assertSame($this->parser->parseMetadataValue($value), $actual);
        } else {
            Assert::assertJsonStringEqualsJsonString($this->parser->parseMetadataValue($value), json_encode($actual));
        }
    }

    /**
     * @Given a provider state :state for the message is specified
     */
    public function aProviderStateForTheMessageIsSpecified(string $state): void
    {
        $this->builder->given($state, []);
    }

    /**
     * @Given a message is defined
     */
    public function aMessageIsDefined(): void
    {
        $this->aMessageIntegrationIsBeingDefinedForAConsumerTest();
    }

    /**
     * @Then the first message in the pact file will contain :states provider state(s)
     */
    public function theFirstMessageInThePactFileWillContainProviderStates(int $states): void
    {
        Assert::assertCount($states, $this->pact['messages'][0]['providerStates'] ?? []);
    }

    /**
     * @Then the first message in the Pact file will contain provider state :state
     */
    public function theFirstMessageInThePactFileWillContainProviderState(string $state): void
    {
        $states = array_map(fn (array $state): string => $state['name'], $this->pact['messages'][0]['providerStates']);
        Assert::assertContains($state, $states);
    }

    /**
     * @Given a provider state :state for the message is specified with the following data:
     */
    public function aProviderStateForTheMessageIsSpecifiedWithTheFollowingData(string $state, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $this->builder->given($state, $row);
    }

    /**
     * @Then the provider state :state for the message will contain the following parameters:
     */
    public function theProviderStateForTheMessageWillContainTheFollowingParameters(string $state, TableNode $table): void
    {
        $params = json_decode($table->getHash()[0]['parameters'], true);
        Assert::assertContains([
            'name' => $state,
            'params' => $params,
        ], $this->pact['messages'][0]['providerStates']);
    }

    /**
     * @Given the message is configured with the following:
     */
    public function theMessageIsConfiguredWithTheFollowing(TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $message = new Message();
        $message->setBody(isset($row['body']) ? $this->parser->parseBody($row['body']) : null);
        $message->setMetadata(isset($row['metadata']) ? json_decode($row['metadata'], true) : null);
        $this->messageGeneratorBuilder->build($message, $row['generators']);
        if ($message->hasBody()) {
            $this->builder->withContent($message->getBody());
        }
        if ($message->hasMetadata()) {
            $this->builder->withContent('not empty'); // any not empty text, doesn't matter. If empty or not provided, received message will be null.
            $this->builder->withMetadata($message->getMetadata());
        }
    }

    /**
     * @Then the message contents for :path will have been replaced with a(n) :type
     */
    public function theMessageContentsForWillHaveBeenReplacedWithAn(string $path, string $type): void
    {
        $this->bodyStorage->setBody(json_encode($this->receivedMessage->contents));
        $this->validator->validateType($path, $type);
    }

    /**
     * @Then the received message metadata will contain :key replaced with an :type
     */
    public function theReceivedMessageMetadataWillContainReplacedWithAn(string $key, string $type): void
    {
        $this->bodyStorage->setBody(json_encode($this->receivedMessage->metadata));
        $this->validator->validateType("$.$key", $type);
    }

    public function storeMessage(string $message): void
    {
        $this->receivedMessage = json_decode($message);
    }

    private function process(callable $callback): void
    {
        $this->builder->setCallback($callback);

        $this->verifyResult = $this->builder->verify();
    }
}
