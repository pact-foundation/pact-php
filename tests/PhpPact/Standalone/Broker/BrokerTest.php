<?php

namespace PhpPact\Standalone\Broker;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class BrokerTest extends TestCase
{
    /**
     * @test
     */
    public function getArguments(): void
    {
        $arguments = (new Broker(
            (new BrokerConfig())
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
    //public function generateUuid(): void
    //{
    //    $this->assertContains('-', (new Broker(new BrokerConfig()))->generateUuid());
    //}

    /**
     * @test
     *
     * @throws \Exception
     */
    public function describeVersion(): void
    {
        $config = new BrokerConfig();
        $config->setPacticipant(\rawurlencode('Animal Profile Service'))
            ->setBrokerUri(new Uri('https://test.pactflow.io'))
            ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
            ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1');
        $broker = new Broker($config);

        $result = $broker->describeVersion();

        $this->assertArrayHasKey('number', $result);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function listLatestPactVersions(): void
    {
        $config = new BrokerConfig();
        $config->setPacticipant(\rawurlencode('Animal Profile Service'))
            ->setBrokerUri(new Uri('https://test.pactflow.io'))
            ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
            ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1');
        $broker = new Broker($config);

        $result = $broker->listLatestPactVersions();
        $this->assertArrayHasKey('pacts', $result);
    }
}
