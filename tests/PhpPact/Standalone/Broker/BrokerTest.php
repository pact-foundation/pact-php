<?php

namespace PhpPact\Standalone\Broker;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class BrokerTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function publish(): void
    {
        $this->expectNotToPerformAssertions();
        (new Broker(
            (new BrokerConfig())
                ->setBrokerUri(new Uri('https://test.pact.dius.com.au'))
                ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
                ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1')
                ->setPactLocations(__DIR__ . '/../../../../example/pacts/')
                ->setConsumerVersion('1.0.0')
                ->setTag('latest')
        ))->publish();
    }

    /**
     * @test
     */
    public function getArguments(): void
    {
        $arguments = (new Broker((new BrokerConfig())
            ->setBrokerToken('someToken')
            ->setBrokerUsername('someusername')
            ->setBrokerPassword('somepassword')
        ))->getArguments();

        $this->assertContains('--broker-token=someToken', $arguments);
        $this->assertContains('--broker-username=someusername', $arguments);
        $this->assertContains('--broker-password=somepassword', $arguments);
    }

    /**
     * @test
     */
    public function getArgumentsEmptyConfig(): void
    {
        $this->assertEmpty((new Broker(new BrokerConfig()))->getArguments());
    }

    /**
     * @test
     */
    public function generateUuid(): void
    {
        $this->assertContains('-', (new Broker(new BrokerConfig()))->generateUuid());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function describeVersion(): void
    {
        $this->assertArrayHasKey('number', (new Broker(
            (new BrokerConfig())
                ->setPacticipant(rawurlencode('Animal Profile Service'))
                ->setBrokerUri(new Uri('https://test.pact.dius.com.au'))
                ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
                ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1')
        ))->describeVersion());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function listLatestPactVersions(): void
    {
        $this->assertArrayHasKey('pacts', (new Broker(
            (new BrokerConfig())
                ->setBrokerUri(new Uri('https://test.pact.dius.com.au'))
                ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
                ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1')
        ))->listLatestPactVersions());
    }
}
