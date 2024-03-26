<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpPactTest\CompatibilitySuite\Service\ProviderStateServerInterface;

final class ProviderStateContext implements Context
{
    public function __construct(
        private ProviderStateServerInterface $providerStateServer
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function startProviderState(BeforeScenarioScope $scope): void
    {
        if (preg_match('/^Verifying .* provider state/', $scope->getScenario()->getTitle())) {
            $this->providerStateServer->start();
        }
    }

    /**
     * @AfterScenario
     */
    public function stopProviderState(AfterScenarioScope $scope): void
    {
        if (preg_match('/^Verifying .* provider state/', $scope->getScenario()->getTitle())) {
            $this->providerStateServer->stop();
        }
    }
}
