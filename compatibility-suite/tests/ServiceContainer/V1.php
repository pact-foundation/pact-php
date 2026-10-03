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
        $interactionsStorage = new InteractionsStorage();
        $matchingRuleConverter = new MatchingRuleConverter();
        $httpClient = new HttpClient();
        $fixtureLoader = new FixtureLoader();
        $parser = new Parser($fixtureLoader, $this->getSpecification());
        $matchingRuleParser = new MatchingRuleParser($matchingRuleConverter, $fixtureLoader);
        $server = new Server($this->getSpecification(), $interactionsStorage);
        $requestBuilder = new RequestBuilder($parser);
        $responseBuilder = new ResponseBuilder($parser);

        $this->set('specification', $this->getSpecification());
        $this->set('interactions_storage', $interactionsStorage);
        $this->set('provider_state_server', new ProviderStateServer());
        $this->set('matching_rule_converter', $matchingRuleConverter);
        $this->set('matching_rules_storage', new MatchingRulesStorage());
        $this->set('http_client', $httpClient);
        $this->set('provider_verifier', new ProviderVerifier());
        $this->set('fixture_loader', $fixtureLoader);
        $this->set('parser', $parser);
        $this->set('pact_broker', new PactBroker());
        $this->set('matching_rule_parser', $matchingRuleParser);
        $this->set('server', $server);
        $this->set('request_builder', $requestBuilder);
        $this->set('response_builder', $responseBuilder);
        $this->set('request_matching_rule_builder', new RequestMatchingRuleBuilder($matchingRuleParser, $matchingRuleConverter));
        $this->set('response_matching_rule_builder', new ResponseMatchingRuleBuilder($matchingRuleParser, $matchingRuleConverter));
        $this->set('interaction_builder', new InteractionBuilder($requestBuilder, $responseBuilder));
        $this->set('client', new Client($server, $interactionsStorage, $httpClient));
        $this->set('pact_writer', new PactWriter($interactionsStorage, $this->getSpecification()));
    }

    protected function getSpecification(): string
    {
        return '1.0.0';
    }
}
