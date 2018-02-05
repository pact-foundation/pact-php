<?php

namespace PhpPact\Standalone\ProviderVerifier;

use GuzzleHttp\Psr7\Uri;
use Mockery;
use PhpPact\Broker\Service\BrokerHttpServiceInterface;
use PhpPact\Standalone\Installer\InstallManager;
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
            ->setFormat('someformat');

        $brokerHttpService = Mockery::mock(BrokerHttpServiceInterface::class);

        /** @var BrokerHttpServiceInterface $brokerHttpService */
        $server     = new Verifier($config, $brokerHttpService, new InstallManager());
        $arguments  = $server->getArguments();

        $this->assertTrue(\in_array('--provider-base-url=http://myprovider:1234', $arguments));
        $this->assertTrue(\in_array('--provider-states-setup-url=http://someurl:1234', $arguments));
        $this->assertTrue(\in_array('--publish-verification-results', $arguments));
        $this->assertTrue(\in_array('--broker-username=someusername', $arguments));
        $this->assertTrue(\in_array('--broker-password=somepassword', $arguments));
        $this->assertTrue(\in_array('--custom-provider-header=key1: value1', $arguments));
        $this->assertTrue(\in_array('--custom-provider-header=key2: value2', $arguments));
        $this->assertTrue(\in_array('--verbose', $arguments));
        $this->assertTrue(\in_array('--format=someformat', $arguments));
    }

    public function testGetArgumentsEmptyConfig()
    {
        $config            = new VerifierConfig();
        $brokerHttpService = Mockery::mock(BrokerHttpServiceInterface::class);

        /** @var BrokerHttpServiceInterface $brokerHttpService */
        $server     = new Verifier($config, $brokerHttpService, new InstallManager());
        $arguments  = $server->getArguments();

        $this->assertEmpty($arguments);
    }
}
