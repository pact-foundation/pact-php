<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\ProviderStateContext;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V3\Message\ProviderContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v3_message_provider', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V3',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                ProviderStateContext::class,
                [
                    '@provider_state_server',
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
                    '@interaction_builder',
                    '@interactions_storage',
                    '@message_pact_writer',
                    '@provider_verifier',
                    '@parser',
                    '@fixture_loader',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V3/message_provider.feature')
            ->withFilter(new TagFilter('~@wip'))));
