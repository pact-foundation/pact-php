<?php

namespace PhpPactTest\CompatibilitySuite\Context\Shared;

use Behat\Behat\Context\Context;
use PhpPact\Consumer\Model\Interaction;
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
            $this->storeInteractionWithoutMatchingRules($id, $interaction);
            $this->storeInteractionWithMatchingRules($id, $interaction);
        }
    }

    private function storeInteractionWithoutMatchingRules(int $id, Interaction $interaction): void
    {
        $this->storage->add(InteractionsStorageInterface::CLIENT_DOMAIN, $id, $interaction, true);
    }

    private function storeInteractionWithMatchingRules(int $id, Interaction $interaction): void
    {
        $this->buildMatchingRules($id, $interaction);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $id, $interaction, true);
        $this->storage->add(InteractionsStorageInterface::PACT_WRITER_DOMAIN, $id, $interaction, true);
    }

    private function buildMatchingRules(int $id, Interaction $interaction): void
    {
        if ($file = $this->matchingRulesStorage->get(MatchingRulesStorageInterface::REQUEST_DOMAIN, $id)) {
            $this->requestMatchingRuleBuilder->build($interaction->getRequest(), $file);
        }
        if ($file = $this->matchingRulesStorage->get(MatchingRulesStorageInterface::RESPONSE_DOMAIN, $id)) {
            $this->responseMatchingRuleBuilder->build($interaction->getResponse(), $file);
        }
    }
}
