<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V3\RequestMatchingContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v3_matching_rules', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V3',
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
            ->withPaths(
                '%paths.base%/compatibility-suite/pact-compatibility-suite/features/V3/matching_rules.feature',
                '%paths.base%/compatibility-suite/pact-compatibility-suite/features/V3/http_matching.feature'
            )));
