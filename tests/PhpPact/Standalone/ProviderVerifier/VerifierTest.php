<?php

namespace PhpPact\Standalone\ProviderVerifier;

use Mockery;
use PhpPact\Broker\Service\BrokerHttpServiceInterface;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    public function testBuildParametersNoOptionals()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('SomeProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderBaseUrl('http://myprovider:1234');

        $brokerHttpService = Mockery::mock(BrokerHttpServiceInterface::class);
        $brokerHttpService
            ->shouldReceive('getAllConsumerUrls')
            ->once()
            ->with('SomeProvider', '1.0.0')
            ->andReturn([
                'http://something:1234/something',
                'http://example.com/stuff'
            ]);

        /** @var BrokerHttpServiceInterface $brokerHttpService */
        $server     = new Verifier($config, $brokerHttpService, new InstallManager());
        $parameters = $server->getParameters();

        $this->assertTrue(\in_array('http://something:1234/something', $parameters));
        $this->assertTrue(\in_array('http://example.com/stuff', $parameters));
        $this->assertTrue(in_array("--provider-base-url=http://myprovider:1234", $parameters));
    }
}
