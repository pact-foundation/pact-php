<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared;

use Behat\Behat\Context\Context;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MatchingRulesStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestMatchingRuleBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ResponseMatchingRuleBuilderInterface;

class InteractionsContext implements Context
{
    public function __construct(
        private InteractionsStorageInterface $storage,
        private RequestMatchingRuleBuilderInterface $requestMatchingRuleBuilder,
        private ResponseMatchingRuleBuilderInterface $responseMatchingRuleBuilder,
        private MatchingRulesStorageInterface $matchingRulesStorage,
    ) {
    }

    /**
     * @Given the following HTTP interactions have been defined:
     */
    public function theFollowingHttpInteractionsHaveBeenDefined(array $interactions): void
    {
        foreach ($interactions as $id => $interaction) {
            $this->storage->add(InteractionsStorageInterface::MOCK_SERVER_DOMAIN, $id, $interaction);
            $this->storage->add(InteractionsStorageInterface::MOCK_SERVER_CLIENT_DOMAIN, $id, $interaction, true);
            $this->storage->add(InteractionsStorageInterface::PROVIDER_DOMAIN, $id, $interaction, true);
            $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $id, $interaction);
            if ($file = $this->matchingRulesStorage->get(MatchingRulesStorageInterface::REQUEST_DOMAIN, $id)) {
                $this->requestMatchingRuleBuilder->build($interaction->getRequest(), $file);
            }
            if ($file = $this->matchingRulesStorage->get(MatchingRulesStorageInterface::RESPONSE_DOMAIN, $id)) {
                $this->responseMatchingRuleBuilder->build($interaction->getResponse(), $file);
            }
        }
    }
}
