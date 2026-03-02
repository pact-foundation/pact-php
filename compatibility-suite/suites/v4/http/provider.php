<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\InteractionsContext as SharedInteractionsContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\Transform\InteractionsContext as TransformInteractionsContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\ProviderContext as SharedProviderContext;
use PhpPactTest\CompatibilitySuite\Context\V1\Http\ProviderContext as V1ProviderContext;
use PhpPactTest\CompatibilitySuite\Context\V4\Http\ProviderContext as V4ProviderContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_http_provider', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                SharedInteractionsContext::class,
                [
                    '@interactions_storage',
                    '@request_matching_rule_builder',
                    '@response_matching_rule_builder',
                    '@matching_rules_storage',
                ]
            )
            ->addContext(
                TransformInteractionsContext::class,
                [
                    '@interaction_builder',
                    '@matching_rules_storage',
                ]
            )
            ->addContext(
                SharedProviderContext::class,
                [
                    '@server',
                    '@provider_verifier',
                    '@provider_state_server',
                ]
            )
            ->addContext(
                V1ProviderContext::class,
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
                V4ProviderContext::class,
                [
                    '@pact_writer',
                    '@provider_verifier',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/http_provider.feature')));
