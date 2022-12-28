<?php

namespace PhpPactTest\Consumer\Model;

use PhpPact\Consumer\Model\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testSetters()
    {
        $id                  = 123;
        $description         = 'a message';
        $providerStateName   = 'a provider state';
        $providerStateParams = ['foo' => 'bar'];
        $metadata            = ['queue' => 'foo', 'routing_key' => 'bar'];
        $contents            = 'test';

        $subject = (new Message())
            ->setId($id)
            ->setDescription($description)
            ->addProviderState($providerStateName, $providerStateParams)
            ->setMetadata($metadata)
            ->setContents($contents);

        static::assertSame($id, $subject->getId());
        static::assertSame($description, $subject->getDescription());
        static::assertEquals([(object) ['name' => $providerStateName, 'params' => $providerStateParams]], $subject->getProviderStates());
        static::assertSame($metadata, $subject->getMetadata());
        static::assertSame($contents, $subject->getContents());
    }
}
