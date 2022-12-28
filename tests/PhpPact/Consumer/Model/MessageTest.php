<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\Message;
use PhpPact\Consumer\Model\ProviderState;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testSetters()
    {
        $description         = 'a message';
        $providerStateName   = 'a provider state';
        $providerStateParams = ['foo' => 'bar'];
        $metadata            = ['queue' => 'foo', 'routing_key' => 'bar'];
        $contents            = 'test';

        $subject = (new Message())
            ->setDescription($description)
            ->addProviderState($providerStateName, $providerStateParams)
            ->setMetadata($metadata)
            ->setContents($contents);

        static::assertSame($description, $subject->getDescription());
        $providerStates = $subject->getProviderStates();
        static::assertCount(1, $providerStates);
        static::assertContainsOnlyInstancesOf(ProviderState::class, $providerStates);
        static::assertEquals($providerStateName, $providerStates[0]->getName());
        static::assertEquals($providerStateParams, $providerStates[0]->getParams());
        static::assertSame($metadata, $subject->getMetadata());
        static::assertSame($contents, $subject->getContents());
    }
}
