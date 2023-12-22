<?php

namespace PhpPactTest\CompatibilitySuite\Context\V2\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MatchingRulesStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\RequestMatchingRuleBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;

final class ConsumerContext implements Context
{
    public function __construct(
        private ServerInterface $server,
        private RequestBuilderInterface $requestBuilder,
        private RequestMatchingRuleBuilderInterface $requestMatchingRuleBuilder,
        private MatchingRulesStorageInterface $matchingRulesStorage,
        private InteractionsStorageInterface $storage,
    ) {
    }

    /**
     * @When the mock server is started with interaction :id but with the following changes:
     */
    public function theMockServerIsStartedWithInteractionButWithTheFollowingChanges(int $id, TableNode $table): void
    {
        $request = $this->storage->get(InteractionsStorageInterface::SERVER_DOMAIN, $id)->getRequest();
        $this->requestBuilder->build($request, $table->getHash()[0]);
        if ($file = $this->matchingRulesStorage->get(MatchingRulesStorageInterface::REQUEST_DOMAIN, $id)) {
            $this->requestMatchingRuleBuilder->build($request, $file);
        }
        $this->server->register($id);
    }
}
