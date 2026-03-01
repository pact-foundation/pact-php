<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\Transform\InteractionsContext;
use PhpPactTest\CompatibilitySuite\Context\V4\Http\ProviderContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_http_provider', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
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
                ProviderContext::class,
                [
                    '@server',
                    '@provider_verifier',
                    '@provider_state_server',
                ]
            )
            ->addContext(
                ProviderContext::class,
                [
                    '@server',
                    '@pact_writer',
                    '@pact_broker',
                    '@response_builder',
                    '@interactions_storage',
                    '@provider_verifier',
                ]
            )
            ->addContext(
                ProviderContext::class,
                [
                    '@pact_writer',
                    '@provider_verifier',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/http_provider.feature')));
