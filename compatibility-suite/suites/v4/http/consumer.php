<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V4\Http\ConsumerContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_http_consumer', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                ConsumerContext::class,
                [
                    '@interaction_builder',
                    '@pact_writer',
                    '@interactions_storage',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/http_consumer.feature')));
