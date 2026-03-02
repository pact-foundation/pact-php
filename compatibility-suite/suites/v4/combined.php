<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V4\CombinedContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_combined', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                CombinedContext::class,
                [
                    '@interaction_builder',
                    '@interactions_storage',
                    '@pact_writer',
                    '@message_pact_writer',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/v4.feature')));
