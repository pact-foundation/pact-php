<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Message;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    public function __construct(
        private MessagePactWriterInterface $pactWriter
    ) {
    }

    /**
     * @Given a message interaction is being defined for a consumer test
     */
    public function aMessageInteractionIsBeingDefinedForAConsumerTest(): void
    {
    }

    /**
     * @When the Pact file for the test is generated
     */
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->pactWriter->write('a message', '');
    }

    /**
     * @Then the first interaction in the Pact file will have a type of :type
     */
    public function theFirstInteractionInThePactFileWillHaveATypeOf(string $type): void
    {
        $pact = json_decode(file_get_contents($this->pactWriter->getPactPath()), true);
        Assert::assertSame($type, $pact['interactions'][0]['type']);
    }

    /**
     * @Given a key of :key is specified for the message interaction
     */
    public function aKeyOfIsSpecifiedForTheMessageInteraction(string $key): void
    {
        throw new PendingException("Can't set message's key using FFI call");
    }

    /**
     * @Given the message interaction is marked as pending
     */
    public function theMessageInteractionIsMarkedAsPending(): void
    {
        throw new PendingException("Can't set message's pending using FFI call");
    }

    /**
     * @Given a comment :value is added to the message interaction
     */
    public function aCommentIsAddedToTheMessageInteraction(string $value): void
    {
        throw new PendingException("Can't set message's comment using FFI call");
    }

    /**
     * @Then the first interaction in the Pact file will have :name = :value
     */
    public function theFirstInteractionInThePactFileWillHave(string $name, string $value): void
    {
        $pact = json_decode(file_get_contents($this->pactWriter->getPactPath()), true);
        Assert::assertSame($value, $pact['interactions'][0][$name]);
    }
}
