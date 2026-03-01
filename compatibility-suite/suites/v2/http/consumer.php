<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\Transform\InteractionsContext;
use PhpPactTest\CompatibilitySuite\Context\V2\Http\ConsumerContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v2_http_consumer', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V2',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                InteractionsContext::class,
                [
                    '@interactions_storage',
                    '@request_matching_rule_builder',
                    '@response_matching_rule_builder',
                    '@matching_rules_storage',
                ]
            )
            ->addContext(
                InteractionsContext::class,
                [
                    '@interaction_builder',
                    '@matching_rules_storage',
                ]
            )
            ->addContext(
                ConsumerContext::class,
                [
                    '@server',
                    '@request_builder',
                    '@client',
                    '@interactions_storage',
                    '@fixture_loader',
                ]
            )
            ->addContext(
                ConsumerContext::class,
                [
                    '@server',
                    '@request_builder',
                    '@request_matching_rule_builder',
                    '@matching_rules_storage',
                    '@interactions_storage',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V2/http_consumer.feature')));
