<?php

namespace PhpPact\Standalone\ProviderVerifier;

use GuzzleHttp\Psr7\Uri;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Broker\Service\BrokerHttpClientInterface;
use PhpPact\Standalone\Installer\InstallManager;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;

class VerifierTest extends TestCase
{
    public function testGetArguments()
    {
        $config = new VerifierConfig();
        $config
            ->setProviderName('someProvider')
            ->setProviderVersion('1.0.0')
            ->setProviderVersionTag('prod')
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
        $this->assertContains('--provider-version-tag=prod', $arguments);
        $this->assertSame(['process_timeout' => 30, 'process_idle_timeout' => 5], $server->getTimeoutValues());
    }

    public function testGetArgumentsEmptyConfig()
    {
        $this->assertEmpty((new Verifier(new VerifierConfig()))->getArguments());
    }

    /**
     * @dataProvider dataProviderForBrokerPathTest
     *
     * @param string      $consumerName
     * @param string      $providerName
     * @param null|string $tag
     * @param null|string $version
     * @param string      $path
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function testBuildValidPathToPactBroker($consumerName, $providerName, $tag, $version, $path)
    {
        $expectedUrltoBroker = 'http://mock/' . $path;

        /** @var Uri $uriMock */
        $uriMock = $this->createMock(Uri::class);
        $uriMock->expects($this->once())
            ->method('withPath')
            ->with($path)
            ->willReturn($uriMock);

        $uriMock->expects($this->once())
            ->method('__toString')
            ->willReturn($expectedUrltoBroker);

        $installerMock = $this->createMock(InstallManager::class);

        $verifierProcessMock = $this->createMock(VerifierProcess::class);
        $verifierProcessMock->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(function ($args) use ($expectedUrltoBroker) {
                    return \in_array($expectedUrltoBroker, $args);
                })
            );

        $config = new VerifierConfig();
        $config->setProviderName($providerName)
            ->setProviderBaseUrl(new Uri('http://myprovider:1234'))
            ->setProviderStatesSetupUrl(new Uri('http://someurl:1234'))
            ->setBrokerUri($uriMock)
            ->setVerbose(true);

        $verifier = new Verifier($config, $installerMock, $verifierProcessMock);

        $verifier->verify($consumerName, $tag, $version);
    }

    public function dataProviderForBrokerPathTest()
    {
        $consumerName = 'someProviderName';
        $providerName = 'someProviderName';
        $tag          = '1.0.0';
        $version      = '11111';

        return [
            [$consumerName, $providerName, null, $version, "pacts/provider/$providerName/consumer/$consumerName/version/$version/"],
            [$consumerName, $providerName, $tag, null, "pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, $tag, $version, "pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, null, null, "pacts/provider/$providerName/consumer/$consumerName/latest/"],
        ];
    }

    /**
     * @dataProvider provideDataForVerifyAll
     *
     * @param string $providerName
     * @param string $providerVersion
     * @param bool   $forceLatest
     * @param mixed  $expectedProviderVersion
     *
     * @throws \PhpPact\Standalone\Installer\Exception\FileDownloadFailureException
     * @throws \PhpPact\Standalone\Installer\Exception\NoDownloaderFoundException
     */
    public function testIfDataForVerifyAllIsConvertedCorrectly($providerName, $providerVersion)
    {
        $expectedUrl1     = 'expectedUrl1';
        $expectedUrl2     = 'expectedUrl2';
        $expectedPactUrls = [$expectedUrl1, $expectedUrl2];

        $installerMock = $this->createMock(InstallManager::class);

        $verifierProcessMock = $this->createMock(VerifierProcess::class);
        $verifierProcessMock->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(function ($args) use ($expectedUrl1, $expectedUrl2) {
                    return \in_array($expectedUrl1, $args) && \in_array($expectedUrl2, $args);
                })
            );

        $brokerHttpClient = $this->createMock(BrokerHttpClient::class);

        $brokerHttpClient->expects($this->once())
        ->method('getAllConsumerUrls')
            ->with($this->equalTo($providerName))
            ->will($this->returnValue($expectedPactUrls));

        $config = new VerifierConfig();
        $config->setProviderName($providerName);
        $config->setProviderVersion($providerVersion);

        $verifier = new Verifier($config, $installerMock, $verifierProcessMock, $brokerHttpClient);
        $verifier->verifyAll();
    }

    public function provideDataForVerifyAll()
    {
        return [
          ['someProvider', '1.0.0'],
          ['someProvider', '1.2.3'],
        ];
    }

    public function testRunShouldLogOutputIfCmdFails()
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $cmd = __DIR__ . \DIRECTORY_SEPARATOR . 'verifier.sh';
        } else {
            $cmd = 'cmd /c' . __DIR__ . \DIRECTORY_SEPARATOR . 'verifier.bat';
        }

        $scriptsMock = $this->createMock(Scripts::class);
        $scriptsMock->method('getProviderVerifier')->willReturn($cmd);

        $installerMock = $this->createMock(InstallManager::class);
        $installerMock->method('install')->willReturn($scriptsMock);

        $process = new VerifierProcess($installerMock);

        $logger = new Logger('console', [$handler = new TestHandler()]);
        $process->setLogger($logger);

        try {
            $exception = null;
            $process->run([], 60, 10);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $logMessages = $handler->getRecords();

        $this->assertGreaterThan(2, \count($logMessages));
        $this->assertContains('first line', $logMessages[\count($logMessages) - 2]['message']);
        $this->assertContains('second line', $logMessages[\count($logMessages) - 1]['message']);

        $this->assertNotNull($exception);
    }
}
