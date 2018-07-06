<?php

namespace PhpPact\Standalone\ProviderVerifier;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Broker\Service\BrokerHttpClientInterface;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    public function testGetArguments()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('SomeProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderBaseUrl(new Uri('http://myprovider:1234'))
            ->setProviderStatesSetupUrl(new Uri('http://someurl:1234'))
            ->setPublishResults(true)
            ->setBrokerUsername('someusername')
            ->setBrokerPassword('somepassword')
            ->addCustomProviderHeader('key1', 'value1')
            ->addCustomProviderHeader('key2', 'value2')
            ->setVerbose(true)
            ->setFormat('someformat')
            ->setProcessTimeout(30)
            ->setProcessIdleTimeout(5);

        /** @var BrokerHttpClientInterface $brokerHttpService */
        $server    = new Verifier($config);
        $arguments = $server->getArguments();

        $this->assertContains('--provider-base-url=http://myprovider:1234', $arguments);
        $this->assertContains('--provider-states-setup-url=http://someurl:1234', $arguments);
        $this->assertContains('--publish-verification-results', $arguments);
        $this->assertContains('--broker-username=someusername', $arguments);
        $this->assertContains('--broker-password=somepassword', $arguments);
        $this->assertContains('--custom-provider-header=key1: value1', $arguments);
        $this->assertContains('--custom-provider-header=key2: value2', $arguments);
        $this->assertContains('--verbose', $arguments);
        $this->assertContains('--format=someformat', $arguments);
        $this->assertSame(['process_timeout' => 30, 'process_idle_timeout' => 5], $server->getTimeoutValues());
    }

    public function testGetArgumentsEmptyConfig()
    {
        $this->assertEmpty((new Verifier(new VerifierConfig()))->getArguments());
    }

    /**
     * @dataProvider dataProviderForBrokerPathTest
     */
    public function testBuildValidPathToPactBroker($consumerName, $providerName, $tag, $verison, $path)
    {
        $uriMock = $this->createMock(Uri::class);
        $uriMock->expects($this->once())
            ->method('withPath')
            ->with($path)
            ->willReturn($uriMock);

        $config = new VerifierConfig();
        $config->setProviderName($providerName);
        $config->setBrokerUri($uriMock);

        $verifier = new Verifier($config);
        $verifier->verify($consumerName, $tag, $verison);
    }

    public function dataProviderForBrokerPathTest()
    {
        $consumerName = 'someProviderName';
        $providerName = 'someProviderName';
        $tag = '1.0.0';
        $version = '11111';

        return [
            [$consumerName, $providerName, null, $version, "pacts/provider/$providerName/consumer/$consumerName/version/$version/"],
            [$consumerName, $providerName, $tag, null, "pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, $tag, $version, "pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, null, null, "pacts/provider/$providerName/consumer/$consumerName/latest/"],
        ];
    }
}