<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4;

use Behat\Behat\Context\Context;
use PhpPact\Config\PactConfigInterface;
use PhpPact\Consumer\Model\Interaction;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PHPUnit\Framework\Assert;

final class CombinedContext implements Context
{
    private int $id = 1;

    public function __construct(
        private InteractionBuilderInterface $builder,
        private InteractionsStorageInterface $storage,
        private PactWriterInterface $pactWriter,
        private MessagePactWriterInterface $messagePactWriter,
    ) {
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
        $this->pactWriter->write($this->id, 'c', 'p', PactConfigInterface::MODE_MERGE);
        $this->messagePactWriter->write('message interaction', '', 'c', 'p', PactConfigInterface::MODE_MERGE);
    }

    /**
     * @Then there will be an interaction in the Pact file with a type of :type
     */
    public function thereWillBeAnInteractionInThePactFileWithATypeOf(string $type): void
    {
        $pact = json_decode(file_get_contents($this->pactWriter->getPactPath()), true);
        $types = array_map(fn (array $interaction) => $interaction['type'], $pact['interactions']);
        Assert::assertContains($type, $types);
    }
}
