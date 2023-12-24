<?php

namespace PhpPactTest\CompatibilitySuite\Context\V3\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
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
     * @Given an integration is being defined for a consumer test
     */
    public function anIntegrationIsBeingDefinedForAConsumerTest(): void
    {
        $this->interaction = $this->builder->build([
            'description' => 'interaction for a consumer test',
            'method' => 'GET',
            'path' => '/provider-states',
        ]);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $this->id, $this->interaction);
    }

    /**
     * @Given a provider state :state is specified
     */
    public function aProviderStateIsSpecified(string $state): void
    {
        $this->interaction->addProviderState($state, []);
    }

    /**
     * @When the Pact file for the test is generated
     */
    public function thePactFileForTheTestIsGenerated(): void
    {
        $this->pactWriter->write($this->id, $this->pactPath);
    }

    /**
     * @Then the interaction in the Pact file will contain :states provider state(s)
     */
    public function theInteractionInThePactFileWillContainProviderStates(int $states): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertCount($states, $pact['interactions'][0]['providerStates']);
    }

    /**
     * @Then the interaction in the Pact file will contain provider state :name
     */
    public function theInteractionInThePactFileWillContainProviderState(string $name): void
    {
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertNotEmpty(array_filter(
            $pact['interactions'][0]['providerStates'],
            fn (array $providerState) => $providerState['name'] === $name
        ));
    }

    /**
     * @Given a provider state :state is specified with the following data:
     */
    public function aProviderStateIsSpecifiedWithTheFollowingData(string $state, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $this->interaction->addProviderState($state, $row);
    }

    /**
     * @Then the provider state :name in the Pact file will contain the following parameters:
     */
    public function theProviderStateInThePactFileWillContainTheFollowingParameters(string $name, TableNode $table): void
    {
        $rows = $table->getHash();
        $row = reset($rows);
        $params = json_decode($row['parameters'], true);
        $pact = json_decode(file_get_contents($this->pactPath), true);
        Assert::assertNotEmpty(array_filter(
            $pact['interactions'][0]['providerStates'],
            fn (array $providerState) => $providerState['name'] === $name && $providerState['params'] === $params
        ));
    }
}
