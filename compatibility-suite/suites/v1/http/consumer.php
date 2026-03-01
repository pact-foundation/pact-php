<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\Transform\InteractionsContext;
use PhpPactTest\CompatibilitySuite\Context\V1\Http\ConsumerContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v1_http_consumer', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V1',
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
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V1/http_consumer.feature')));
