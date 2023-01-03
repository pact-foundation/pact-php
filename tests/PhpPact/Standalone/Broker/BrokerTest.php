<?php

namespace PhpPactTest\Standalone\Broker;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\Broker\Broker;
use PhpPact\Standalone\Broker\BrokerConfig;
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

        $this->assertSame([
            '--broker-token',
            'someToken',
            '--broker-username',
            'someusername',
            '--broker-password',
            'somepassword',
        ], $arguments);
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
        $config->setPacticipant('Animal Profile Service')
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
        $config->setPacticipant("\"Animal Profile Service\"")
            ->setBrokerUri(new Uri('https://test.pactflow.io'))
            ->setBrokerUsername('dXfltyFMgNOFZAxr8io9wJ37iUpY42M')
            ->setBrokerPassword('O5AIZWxelWbLvqMd8PkAVycBJh2Psyg1');
        $broker = new Broker($config);

        $result = $broker->listLatestPactVersions();
        $this->assertArrayHasKey('pacts', $result);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function publishLogsStdError(): void
    {
        $config = new BrokerConfig();
        $config->setPactLocations('not a directory');
        $broker = new Broker($config);
        try {
            $broker->publish();
        } catch(\Exception $e) {
            $this->assertEquals(1, $e->getCode());
            $this->assertStringContainsString("PactPHP Process returned non-zero exit code: 1", $e->getMessage());
        }
    }
}
