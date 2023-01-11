<?php

namespace PhpPact\Standalone\ProviderVerifier;

use GuzzleHttp\Psr7\Uri;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Broker\Service\BrokerHttpClientInterface;
use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class VerifierTest extends TestCase
{
    public function testGetArguments()
    {
        $consumerVersionSelectors = (new ConsumerVersionSelectors())
            ->addSelector('{"tag":"foo","latest":true}')
            ->addSelector('{"tag":"bar","latest":true}');

        $config = new VerifierConfig();
        $config
            ->setProviderName('some provider with whitespace')
            ->setProviderVersion('1.0.0')
            ->setProviderBranch('main')
            ->addProviderVersionTag('prod')
            ->addProviderVersionTag('dev')
            ->addConsumerVersionTag('dev')
            ->setProviderBaseUrl(new Uri('http://myprovider:1234'))
            ->setProviderStatesSetupUrl(new Uri('http://someurl:1234'))
            ->setPublishResults(true)
            ->setBrokerToken('someToken')
            ->setBrokerUsername('someusername')
            ->setBrokerPassword('somepassword')
            ->setBrokerUri(new Uri('https://example.broker/'))
            ->addCustomProviderHeader('key1', 'value1')
            ->addCustomProviderHeader('key2', 'value2')
            ->setVerbose(true)
            ->setLogDirectory('my/log/directory')
            ->setFormat('someformat')
            ->setProcessTimeout(30)
            ->setProcessIdleTimeout(5)
            ->setEnablePending(true)
            ->setIncludeWipPactSince('2020-01-30')
            ->setRequestFilter(
                function (RequestInterface $r) {
                    return $r->withHeader('MY_SPECIAL_HEADER', 'my special value');
                }
            )
            ->setConsumerVersionSelectors($consumerVersionSelectors);

        /** @var BrokerHttpClientInterface $brokerHttpService */
        $server    = new Verifier($config);
        $arguments = $server->getArguments();

        $this->assertContains('--provider-base-url=http://myprovider:1234', $arguments);
        $this->assertContains('--provider-states-setup-url=http://someurl:1234', $arguments);
        $this->assertContains('--publish-verification-results', $arguments);
        $this->assertContains('--broker-token=someToken', $arguments);
        $this->assertContains('--broker-username=someusername', $arguments);
        $this->assertContains('--broker-password=somepassword', $arguments);
        $this->assertContains('--custom-provider-header="key1: value1"', $arguments);
        $this->assertContains('--custom-provider-header="key2: value2"', $arguments);
        $this->assertContains('--verbose=VERBOSE', $arguments);
        $this->assertContains('--log-dir=my/log/directory', $arguments);
        $this->assertContains('--format=someformat', $arguments);
        $this->assertContains('--provider-version-tag=prod', $arguments);
        $this->assertContains('--provider-version-tag=dev', $arguments);
        $this->assertContains('--provider-version-branch=main', $arguments);
        $this->assertContains('--consumer-version-tag=dev', $arguments);
        $this->assertSame(['process_timeout' => 30, 'process_idle_timeout' => 5], $server->getTimeoutValues());
        $this->assertContains('--enable-pending', $arguments);
        $this->assertContains('--include-wip-pacts-since=2020-01-30', $arguments);
        $this->assertContains('--consumer-version-selector=\'{"tag":"foo","latest":true}\'', $this->stripSpaces($arguments));
        $this->assertContains('--consumer-version-selector=\'{"tag":"bar","latest":true}\'', $this->stripSpaces($arguments));
        $this->assertContains('--provider=\'some provider with whitespace\'', $arguments);
        $this->assertContains('--pact-broker-base-url=https://example.broker/', $arguments);
    }

    /**
     * Strip spaces for Windows CMD
     */
    private function stripSpaces($arr)
    {
        $newArr = [];
        foreach ($arr as $str) {
            $newArr[] = str_ireplace(' ', '', $str);
        }
        return $newArr;
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

        $uriMock->expects($this->any())
            ->method('__toString')
            ->willReturn($expectedUrltoBroker);

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

        $verifier = new Verifier($config, $verifierProcessMock);

        $verifier->verify($consumerName, $tag, $version);
    }

    public function dataProviderForBrokerPathTest()
    {
        $consumerName = 'someProviderName';
        $providerName = 'someProviderName';
        $tag          = '1.0.0';
        $version      = '11111';

        return [
            [$consumerName, $providerName, null, $version, "/pacts/provider/$providerName/consumer/$consumerName/version/$version/"],
            [$consumerName, $providerName, $tag, null, "/pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, $tag, $version, "/pacts/provider/$providerName/consumer/$consumerName/latest/$tag/"],
            [$consumerName, $providerName, null, null, "/pacts/provider/$providerName/consumer/$consumerName/latest/"],
        ];
    }

    /**
     * @dataProvider provideDataForVerifyAll
     *
     * @param string $providerName
     * @param string $providerVersion
     * @param bool   $forceLatest
     * @param mixed  $expectedProviderVersion
     */
    public function testIfDataForVerifyAllIsConvertedCorrectly($providerName, $providerVersion)
    {
        $expectedUrl1     = 'expectedUrl1';
        $expectedUrl2     = 'expectedUrl2';
        $expectedPactUrls = [$expectedUrl1, $expectedUrl2];

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
            ->willReturn($expectedPactUrls);

        $config = new VerifierConfig();
        $config->setProviderName($providerName);
        $config->setProviderVersion($providerVersion);

        $verifier = new Verifier($config, $verifierProcessMock, $brokerHttpClient);
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

        $process = new VerifierProcess(new ProcessRunnerFactory($cmd));

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
        $this->assertStringContainsString('first line', $logMessages[\count($logMessages) - 2]['message']);
        $this->assertStringContainsString('second line', $logMessages[\count($logMessages) - 1]['message']);

        $this->assertNotNull($exception);
    }
}
