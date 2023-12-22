<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

use PhpPactTest\CompatibilitySuite\Service\Client;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoader;
use PhpPactTest\CompatibilitySuite\Service\HttpClient;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilder;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorage;
use PhpPactTest\CompatibilitySuite\Service\MatchingRuleConverter;
use PhpPactTest\CompatibilitySuite\Service\MatchingRuleParser;
use PhpPactTest\CompatibilitySuite\Service\MatchingRulesStorage;
use PhpPactTest\CompatibilitySuite\Service\PactBroker;
use PhpPactTest\CompatibilitySuite\Service\PactWriter;
use PhpPactTest\CompatibilitySuite\Service\Parser;
use PhpPactTest\CompatibilitySuite\Service\ProviderStateServer;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifier;
use PhpPactTest\CompatibilitySuite\Service\RequestBuilder;
use PhpPactTest\CompatibilitySuite\Service\RequestMatchingRuleBuilder;
use PhpPactTest\CompatibilitySuite\Service\ResponseBuilder;
use PhpPactTest\CompatibilitySuite\Service\ResponseMatchingRuleBuilder;
use PhpPactTest\CompatibilitySuite\Service\Server;

class V1 extends AbstractServiceContainer
{
    public function __construct()
    {
        $this->set('specification', $this->getSpecification());
        $this->set('interactions_storage', new InteractionsStorage());
        $this->set('provider_state_server', new ProviderStateServer());
        $this->set('matching_rule_converter', new MatchingRuleConverter());
        $this->set('matching_rules_storage', new MatchingRulesStorage());
        $this->set('http_client', new HttpClient());
        $this->set('provider_verifier', new ProviderVerifier());
        $this->set('fixture_loader', new FixtureLoader());
        $this->set('parser', new Parser($this->get('fixture_loader'), $this->getSpecification()));
        $this->set('pact_broker', new PactBroker($this->getSpecification()));
        $this->set('matching_rule_parser', new MatchingRuleParser($this->get('matching_rule_converter'), $this->get('fixture_loader')));
        $this->set('server', new Server($this->getSpecification(), $this->get('interactions_storage')));
        $this->set('request_builder', new RequestBuilder($this->get('parser')));
        $this->set('response_builder', new ResponseBuilder($this->get('parser')));
        $this->set('request_matching_rule_builder', new RequestMatchingRuleBuilder($this->get('matching_rule_parser'), $this->get('matching_rule_converter')));
        $this->set('response_matching_rule_builder', new ResponseMatchingRuleBuilder($this->get('matching_rule_parser'), $this->get('matching_rule_converter')));
        $this->set('interaction_builder', new InteractionBuilder($this->get('request_builder'), $this->get('response_builder')));
        $this->set('client', new Client($this->get('server'), $this->get('interactions_storage'), $this->get('http_client')));
        $this->set('pact_writer', new PactWriter($this->get('interactions_storage'), $this->getSpecification()));
    }

    protected function getSpecification(): string
    {
        return '1.0.0';
    }
}
