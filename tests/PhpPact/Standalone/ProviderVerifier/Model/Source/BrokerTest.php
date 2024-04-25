<?php

namespace PhpPactTest\Standalone\ProviderVerifier\Model\Source;

use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PHPUnit\Framework\TestCase;

class BrokerTest extends TestCase
{
    public function testSetters(): void
    {
        $enablePending            = true;
        $wipPactSince             = '2020-01-30';
        $providerTags             = ['prod'];
        $providerBranch           = 'main';
        $consumerVersionSelectors = (new ConsumerVersionSelectors())
            ->addSelector('{"tag":"foo","latest":true}')
            ->addSelector('{"tag":"bar","latest":true}');
        $consumerVersionTags      = ['dev'];

        $subject = (new Broker())
            ->setEnablePending($enablePending)
            ->setIncludeWipPactSince($wipPactSince)
            ->setProviderTags($providerTags)
            ->setProviderBranch($providerBranch)
            ->setConsumerVersionSelectors($consumerVersionSelectors)
            ->setConsumerVersionTags($consumerVersionTags);

        static::assertSame($enablePending, $subject->isEnablePending());
        static::assertSame($wipPactSince, $subject->getIncludeWipPactSince());
        static::assertSame($providerTags, $subject->getProviderTags());
        static::assertSame($providerBranch, $subject->getProviderBranch());
        static::assertSame($consumerVersionSelectors, $subject->getConsumerVersionSelectors());
        static::assertSame($consumerVersionTags, $subject->getConsumerVersionTags());
    }
}
