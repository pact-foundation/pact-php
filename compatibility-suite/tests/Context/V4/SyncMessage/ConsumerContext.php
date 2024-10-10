<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\SyncMessage;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use PhpPact\Consumer\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\SyncMessagePactWriterInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private Message $message;
    private array $pact;
    private PactPath $pactPath;

    public function __construct(
        private SyncMessagePactWriterInterface $pactWriter,
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given a synchronous message interaction is being defined for a consumer test
     */
    public function aSynchronousMessageInteractionIsBeingDefinedForAConsumerTest(): void
    {
        $this->message = new Message();
        $this->message->setDescription('a synchronous message');
    }

    /**
     * @When the Pact file for the test is generated
     */
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->pactWriter->write($this->message, $this->pactPath);
    }

    /**
     * @Then the first interaction in the Pact file will have a type of :type
     */
    public function theFirstInteractionInThePactFileWillHaveATypeOf(string $type): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertSame($type, $pact['interactions'][0]['type']);
    }

    /**
     * @Given a key of :key is specified for the synchronous message interaction
     */
    public function aKeyOfIsSpecifiedForTheSynchronousMessageInteraction(string $key): void
    {
        $this->message->setKey($key);
    }

    /**
     * @Given the synchronous message interaction is marked as pending
     */
    public function theSynchronousMessageInteractionIsMarkedAsPending(): void
    {
        $this->message->setPending(true);
    }

    /**
     * @Given a comment :value is added to the synchronous message interaction
     */
    public function aCommentIsAddedToTheSynchronousMessageInteraction(string $value): void
    {
        $this->message->addTextComment($value);
    }

    /**
     * @Then the first interaction in the Pact file will have :name = :value
     */
    public function theFirstInteractionInThePactFileWillHave(string $name, string $value): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertJsonStringEqualsJsonString($value, json_encode($pact['interactions'][0][$name]));
    }

    /**
     * @Given the message request payload contains the :fixture JSON document
     */
    public function theMessageRequestPayloadContainsTheJsonDocument(string $fixture): never
    {
        throw new PendingException("Can't set sync message's request payload using FFI call");
    }

    /**
     * @Given the message response payload contains the :fixture document
     */
    public function theMessageResponsePayloadContainsTheDocument(string $fixture): never
    {
        throw new PendingException("Can't set sync message's response payload using FFI call");
    }

    /**
     * @Then the received message payload will contain the :fixture document
     */
    public function theReceivedMessagePayloadWillContainTheDocument(string $fixture): never
    {
        throw new PendingException('Implement previous pending step first');
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
     * @Then the pact file will contain :num interaction
     */
    public function thePactFileWillContainInteraction(int $num): void
    {
        Assert::assertCount($num, $this->pact['interactions']);
    }

    /**
     * @Then the first interaction in the pact file will contain the :fixture document as the request
     */
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheRequest(string $fixture): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file request content type will be :contentType
     */
    public function theFirstInteractionInThePactFileRequestContentTypeWillBe(string $contentType): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file will contain the :fixture document as a response
     */
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsAResponse(string $fixture): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file response content type will be :contentType
     */
    public function theFirstInteractionInThePactFileResponseContentTypeWillBe(string $contentType): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file will contain :num response messages
     */
    public function theFirstInteractionInThePactFileWillContainResponseMessages(int $num): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file will contain the :fixture document as the first response message
     */
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheFirstResponseMessage(string $fixture): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the first interaction in the pact file will contain the :fixture document as the second response message
     */
    public function theFirstInteractionInThePactFileWillContainTheDocumentAsTheSecondResponseMessage(string $fixture): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Given the message request contains the following metadata:
     */
    public function theMessageRequestContainsTheFollowingMetadata(TableNode $table): never
    {
        throw new PendingException("Can't set sync message's metadata using FFI call");
    }

    /**
     * @Then /^the received message request metadata will contain "([^"]+)" == "(.+)"$/
     */
    public function theReceivedMessageRequestMetadataWillContain(string $key, string $value): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then /^the first message in the pact file will contain the request message metadata "([^"]+)" == "(.+)"$/
     */
    public function theFirstMessageInThePactFileWillContainTheRequestMessageMetadata(string $key, string $value): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Given a provider state :state for the synchronous message is specified
     */
    public function aProviderStateForTheSynchronousMessageIsSpecified(string $state): void
    {
        $this->message->addProviderState($state, []);
    }

    /**
     * @Given a provider state :state for the synchronous message is specified with the following data:
     */
    public function aProviderStateForTheSynchronousMessageIsSpecifiedWithTheFollowingData(string $state, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $this->message->addProviderState($state, $row);
    }

    /**
     * @Then the first message in the pact file will contain :states provider state(s)
     */
    public function theFirstMessageInThePactFileWillContainProviderStates(int $states): void
    {
        Assert::assertCount($states, $this->pact['interactions'][0]['providerStates'] ?? []);
    }

    /**
     * @Then the first message in the Pact file will contain provider state :state
     */
    public function theFirstMessageInThePactFileWillContainProviderState(string $state): void
    {
        $states = array_map(fn (array $state): string => $state['name'], $this->pact['interactions'][0]['providerStates']);
        Assert::assertContains($state, $states);
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
        ], $this->pact['interactions'][0]['providerStates']);
    }

    /**
     * @Given the message request is configured with the following:
     */
    public function theMessageRequestIsConfiguredWithTheFollowing(TableNode $table): never
    {
        throw new PendingException("Can't set sync message's request generators using FFI call");
    }

    /**
     * @Given the message response is configured with the following:
     */
    public function theMessageResponseIsConfiguredWithTheFollowing(TableNode $table): never
    {
        throw new PendingException("Can't set sync message's response generators using FFI call");
    }

    /**
     * @Then the message request contents for :path will have been replaced with a(n) :type
     */
    public function theMessageRequestContentsForWillHaveBeenReplacedWithAn(string $path, string $type): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the message response contents for :path will have been replaced with a(n) :type
     */
    public function theMessageResponseContentsForWillHaveBeenReplacedWithAn(string $path, string $type): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the received message request metadata will contain :key replaced with a(n) :type
     */
    public function theReceivedMessageRequestMetadataWillContainReplacedWithAn(string $key, string $type): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the received message response metadata will contain :key == :value
     */
    public function theReceivedMessageResponseMetadataWillContain(string $key, string $value): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the received message response metadata will contain :key replaced with an :type
     */
    public function theReceivedMessageResponseMetadataWillContainReplacedWithAn(string $key, string $type): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @When the message is successfully processed
     */
    public function theMessageIsSuccessfullyProcessed(): void
    {
        $this->thePactFileForTheTestIsGenerated(); // TODO Implement other pending steps first then update this step
    }

    /**
     * @Then the consumer test will have passed
     */
    public function theConsumerTestWillHavePassed(): never
    {
        throw new PendingException('Implement previous pending step first');
    }

    /**
     * @Then the received message content type will be :contentType
     */
    public function theReceivedMessageContentTypeWillBe(string $contentType): never
    {
        throw new PendingException('Implement previous pending step first');
    }
}
