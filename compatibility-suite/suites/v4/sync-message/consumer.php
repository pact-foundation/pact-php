<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use PhpPactTest\CompatibilitySuite\Context\Shared\Hook\SetUpContext;
use PhpPactTest\CompatibilitySuite\Context\V4\SyncMessage\ConsumerContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('v4_sync_message_consumer', [
            'services' => 'PhpPactTest\CompatibilitySuite\ServiceContainer\V4',
        ]))
            ->addContext(SetUpContext::class)
            ->addContext(
                ConsumerContext::class,
                [
                    '@sync_message_pact_writer',
                ]
            )
            ->withPaths('%paths.base%/compatibility-suite/pact-compatibility-suite/features/V4')
            ->withFilter(new TagFilter('@SynchronousMessage&&@message'))));
