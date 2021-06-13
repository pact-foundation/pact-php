<?php

namespace PhpPact\Standalone\MockServer;

use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class MockServerConfigTest extends TestCase
{
    public function testSetters()
    {
        $provider                 = 'test-provider';
        $consumer                 = 'test-consumer';
        $pactDir                  = 'test-pact-dir/';
        $pactSpecificationVersion = '2.0.0';

        $subject = (new MockServerConfig())
            ->setProvider($provider)
            ->setConsumer($consumer)
            ->setPactDir($pactDir)
            ->setPactSpecificationVersion($pactSpecificationVersion);

        static::assertSame($provider, $subject->getProvider());
        static::assertSame($consumer, $subject->getConsumer());
        static::assertSame($pactDir, $subject->getPactDir());
        static::assertSame($pactSpecificationVersion, $subject->getPactSpecificationVersion());
    }
}
