<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V3\RequestGeneratorsContext;
use PhpPactTest\CompatibilitySuite\Context\V4\BodyGeneratorsContext;
use PhpPactTest\CompatibilitySuite\Context\V4\ResponseGeneratorsContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_generators', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                BodyGeneratorsContext::class,
                [
                    '@body_validator',
                ]
            )
            ->addContext(
                RequestGeneratorsContext::class,
                [
                    '@interaction_builder',
                    '@request_generator_builder',
                    '@interactions_storage',
                    '@pact_writer',
                    '@generator_server',
                    '@provider_verifier',
                    '@body_storage',
                ]
            )
            ->addContext(
                BodyGeneratorsContext::class,
                [
                    '@body_validator',
                ]
            )
            ->addContext(
                ResponseGeneratorsContext::class,
                [
                    '@interaction_builder',
                    '@response_generator_builder',
                    '@interactions_storage',
                    '@server',
                    '@client',
                    '@body_storage',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4/generators.feature')));
