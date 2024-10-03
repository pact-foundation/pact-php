<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4;

use Behat\Behat\Context\Context;
use PhpPact\Config\Enum\WriteMode;
use PhpPact\Consumer\Model\Message;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PHPUnit\Framework\Assert;

final class CombinedContext implements Context
{
    private int $id = 1;
    private PactPath $pactPath;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private InteractionsStorageInterface $storage,
        private PactWriterInterface $pactWriter,
        private MessagePactWriterInterface $messagePactWriter,
    ) {
        $this->pactPath = new PactPath();
    }

    /**
     * @Given an HTTP interaction is being defined for a consumer test
     */
    public function anHttpInteractionIsBeingDefinedForAConsumerTest(): void
    {
        $interaction = $this->builder->build([
            'description' => 'http interaction',
            'method' => 'GET',
            'path' => '/v4-features',
        ]);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $this->id, $interaction);
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
        $this->pactWriter->write($this->id, $this->pactPath, WriteMode::MERGE);
        $message = new Message();
        $message->setDescription('message interaction');
        $this->messagePactWriter->write($message, $this->pactPath, WriteMode::MERGE);
    }

    /**
     * @Then there will be an interaction in the Pact file with a type of :type
     */
    public function thereWillBeAnInteractionInThePactFileWithATypeOf(string $type): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $types = array_map(fn (array $interaction) => $interaction['type'], $pact['interactions']);
        Assert::assertContains($type, $types);
    }
}
