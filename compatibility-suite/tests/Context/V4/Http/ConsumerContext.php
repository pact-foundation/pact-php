<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Http;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use PhpPact\Consumer\Model\Interaction;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PHPUnit\Framework\Assert;

final class ConsumerContext implements Context
{
    private Interaction $interaction;
    private int $id = 1;
    private PactPath $pactPath;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private PactWriterInterface $pactWriter,
        private InteractionsStorageInterface $storage,
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given an HTTP interaction is being defined for a consumer test
     */
    public function anHttpInteractionIsBeingDefinedForAConsumerTest(): void
    {
        $this->interaction = $this->builder->build([
            'description' => 'interaction for a consumer test',
            'method' => 'GET',
            'path' => '/v4-features',
        ]);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $this->id, $this->interaction);
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
     * @Given a key of :key is specified for the HTTP interaction
     */
    public function aKeyOfIsSpecifiedForTheHttpInteraction(string $key): void
    {
        $this->interaction->setKey($key);
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
     * @Given the HTTP interaction is marked as pending
     */
    public function theHttpInteractionIsMarkedAsPending(): void
    {
        $this->interaction->setPending(true);
    }

    /**
     * @Given a comment :value is added to the HTTP interaction
     */
    public function aCommentIsAddedToTheHttpInteraction(string $value): void
    {
        $this->interaction->addTextComment($value);
    }

    /**
     * @When the Pact file for the test is generated
     */
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->pactWriter->write($this->id, $this->pactPath);
    }
}
