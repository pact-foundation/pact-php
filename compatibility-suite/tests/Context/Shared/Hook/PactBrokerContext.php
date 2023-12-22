<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpPactTest\CompatibilitySuite\Service\PactBrokerInterface;

final class PactBrokerContext implements Context
{
    public function __construct(
        private PactBrokerInterface $pactBroker
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function startPactBroker(BeforeScenarioScope $scope): void
    {
        if (str_contains($scope->getScenario()->getTitle(), 'via a Pact broker')) {
            $this->pactBroker->start();
        }
    }

    /**
     * @AfterScenario
     */
    public function stopPactBroker(AfterScenarioScope $scope): void
    {
        if (str_contains($scope->getScenario()->getTitle(), 'via a Pact broker')) {
            $this->pactBroker->stop();
        }
    }
}
