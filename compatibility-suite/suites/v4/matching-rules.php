<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V3\RequestMatchingContext;
use PhpPactTest\CompatibilitySuite\Context\V4\ResponseMatchingContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_matching_rules', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                RequestMatchingContext::class,
                [
                    '@interaction_builder',
                    '@server',
                    '@client',
                    '@interactions_storage',
                    '@request_builder',
                    '@request_matching_rule_builder',
                ]
            )
            ->addContext(
                ResponseMatchingContext::class,
                [
                    '@interaction_builder',
                    '@interactions_storage',
                    '@response_matching_rule_builder',
                    '@server',
                    '@pact_writer',
                    '@provider_verifier',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/matching_rules.feature')));
