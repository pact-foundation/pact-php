<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Message;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PhpPact\Consumer\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\Pact\Pact;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PhpPactTest\CompatibilitySuite\Util\TypeCaster;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private PactPath $pactPath;
    private Message $message;

    public function __construct(
        private MessagePactWriterInterface $pactWriter
    ) {
        $this->pactPath = new PactPath();
    }

    #[Given('a message interaction is being defined for a consumer test')]
    public function aMessageInteractionIsBeingDefinedForAConsumerTest(): void
    {
        $this->message = new Message();
        $this->message->setDescription('a message');
    }

    #[When('the Pact file for the test is generated')]
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->pactWriter->write($this->message, $this->pactPath);
    }

    #[Then('the first interaction in the Pact file will have a type of :type')]
    public function theFirstInteractionInThePactFileWillHaveATypeOf(string $type): void
    {
        $pact = Pact::fromFile($this->pactPath);
        Assert::assertSame($type, $pact->getInteraction(0)->getType());
    }

    #[Given('a key of :key is specified for the message interaction')]
    public function aKeyOfIsSpecifiedForTheMessageInteraction(string $key): void
    {
        $this->message->setKey($key);
    }

    #[Given('the message interaction is marked as pending')]
    public function theMessageInteractionIsMarkedAsPending(): void
    {
        $this->message->setPending(true);
    }

    #[Given('a comment :value is added to the message interaction')]
    public function aCommentIsAddedToTheMessageInteraction(string $value): void
    {
        $this->message->addTextComment($value);
    }

    #[Then('the first interaction in the Pact file will have :name = :value')]
    public function theFirstInteractionInThePactFileWillHave(string $name, string $value): void
    {
        $pact = Pact::fromFile($this->pactPath);
        Assert::assertJsonStringEqualsJsonString($value, TypeCaster::toString(json_encode($pact->getInteraction(0)->getAttribute($name))));
    }
}
